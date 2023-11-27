<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\SupplierRequest;
use App\Models\Price;
use App\Models\Supplier;
use App\Models\SuppliersArea;
use App\Traits\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{

    use Message;

    public function __construct()
    {
        $this->middleware('permission:supplier read', ['only' => ['index','activeSupplier','supplier_details','supplier_orders','supplier_products']]);
        $this->middleware('permission:supplier create', ['only' => ['store','create']]);
        $this->middleware('permission:supplier edit', ['only' => ['update', 'edit','activationSupplier']]);
        $this->middleware('permission:supplier delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $suppliers = Supplier::select('id','name','status','phone','address','is_our_supplier')
        ->getSupplierIfDispatcherAuthenticated()
        ->where(function($q) use($request){
            $q ->when($request->search, function ($q) use ($request) {
                return $q->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('phone', 'like', '%' . $request->search . '%')
                        ->orWhere('address', 'like', '%' . $request->search . '%')
                        ->orWhere('id', 'like', '%' . $request->search . '%');
            });
        })
       ->paginate(10);

        return $this->sendResponse(['suppliers' => $suppliers], 'Data exited successfully');
    }

    /**
     * get active Supplier
     */
    public function activeSupplier()
    {
        $suppliers = Supplier::where('status', 1)->getSupplierIfDispatcherAuthenticated()->get();
        return $this->sendResponse(['suppliers' => $suppliers], 'Data exited successfully');
    }


    public function activationSupplier(Supplier $supplier)
    {
        $supplier->update([
            "status" => $supplier->status == 1 ?  0:1
        ]);
        return $this->sendResponse([], 'Data exited successfully');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SupplierRequest $request)
    {

            $data = collect($request->validated())->except(['days'])->toArray();

            $supplier = Supplier::create($data);
            if($dispatcher = auth()->user()->dispatcher)
                $dispatcher->suppliers()->attach([$supplier->id]);
            foreach($request->days as $day_number)
                $supplier->workdays()->create(['day_number' => $day_number]);

            foreach($request->areas as $area_id)
                SuppliersArea::create(['area_id'=>$area_id , 'supplier_id' => $supplier->id]);

            return $this->sendResponse([], 'Data exited successfully');
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $supplier = Supplier::getSupplierIfDispatcherAuthenticated()->findOrFail($id);
        $areas = SuppliersArea::select(['id','area_id'])->where('supplier_id',$id)->get()->pluck('area_id')->toArray();
        return $this->sendResponse(['supplier' => $supplier,'areas' => $areas,'workdays' => $supplier->workdays->pluck('day_number')->toArray()], 'Data exited successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SupplierRequest $request, $id)
    {

        $data = collect($request->validated())->except(['days'])->toArray();
        $supplier = Supplier::getSupplierIfDispatcherAuthenticated()->findOrFail($id);

        $supplier->update($data);

        $supplier->workdays()->delete();
        foreach($request->days as $day_number)
            $supplier->workdays()->create(['day_number' => $day_number]);

        $supplier->areas()->detach();
        foreach($request->areas as $area_id)
            SuppliersArea::create(['area_id'=>$area_id , 'supplier_id' => $supplier->id]);

        return $this->sendResponse([],'Data exited successfully');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $supplier = Supplier::getSupplierIfDispatcherAuthenticated()->findOrFail($id);

        $supplier->delete();
        return $this->sendResponse([],'Deleted successfully');

    }


    public function supplier_details(Supplier $supplier)
    {
        return response()->json(['supplier' => $supplier]);
    }

    public function supplier_orders(Request $request)
    {
        $items = DB::table('orders')
        ->join('orders_products', 'orders_products.order_id', 'orders.id')
        ->join('prices', 'prices.id', 'orders_products.price_id')
        ->join('products', 'products.id', 'prices.product_id')
        ->join('flavors','flavors.id','products.flavor_id')
        ->join('sizes','sizes.id','products.size_id')
        ->join('measurement_units','measurement_units.id','orders_products.measurement_id')
        ->select(
            'orders_products.price',
            'orders_products.measurement_type',
            'orders_products.measurement_id',
            'products.name_ar as product_ar','products.name_en as product_en',
            'sizes.name_ar as size_ar','sizes.name_en as size_en','prices.id',
            'flavors.name_ar as flavor_ar','flavors.name_en as flavor_en',
            'measurement_units.name_ar as measurement_ar','measurement_units.name_en as measurement_en',

            DB::raw('SUM(sub_total) as total_amount,SUM(orders_products.quantity) as quantity,SUM(orders_products.returned_quantity) as returned_quantity')
        )
        ->whereIn('order_status', ['Completed','Partial Return'])
        ->where('orders.supplier_id', $request->user_id)
        ->where(function ($q) use ($request) {
            $q->when($request->search, function ($q) use ($request) {
                $q->where('products.name_ar', 'like', "%$request->search%")
                    ->orWhere('products.name_en', 'like', "%$request->search%");
            });
        })
        ->where(function ($q) use ($request) {
            $q->when($request->date_from && $request->date_to, function ($q) use ($request) {
                $q->whereBetween('orders.created_at', [$request->date_from, $request->date_to]);
            });
        })
        ->whereNotNull('total_amount')
        ->groupBy('price', 'measurement_type', 'measurement_id', 'id','product_ar','product_en','size_ar','size_en','flavor_ar','flavor_en','measurement_ar','measurement_en')
        ->latest('orders.created_at')
        ->paginate(20);
        return response()->json(['items' => $items], 200);
    }

    public function supplier_products(Request $request)
    {
        $products = Price::with(['logs' => function ($q) {
                $q
                ->where('diff_qty','!=',0)
                ->orWhere('total_qty','!=',0)
                ->orWhere('sold_quantity','!=',0)
                ->orWhere('main_measurement_price','!=',0)
                ->orWhere('sub_measurement_price','!=',0)
                ->orWhere('sub_measurement_price_after_sale','!=',0)
                ->orWhere('main_measurement_price_after_sale','!=',0)
                ->latest();
            }, 'product','product','product.flavor','product.size'])
            ->where('supplier_id', $request->user_id)
            ->where(function($q) use($request){
                $q->when($request->search, function ($q) use ($request) {
                    $q->whereHas('product', function ($q) use ($request) {
                        $q->where('products.name_ar', 'like', "%$request->search%")
                            ->orWhere('products.name_en', 'like', "%$request->search%")
                            ->orWhereRelation('size', function ($q) use ($request) {
                                $q->where('sizes.name_ar', 'like', "%$request->search%")
                                ->orWhere('sizes.name_en', 'like', "%$request->search%");
                            })
                            ->orWhereRelation('flavor', function ($q) use ($request) {
                                $q->where('flavors.name_ar', 'like', "%$request->search%")
                                ->orWhere('flavors.name_en', 'like', "%$request->search%");
                            })
                            ->orWhereRelation('subMeasurementUnit', function ($q) use ($request) {
                                $q->where('measurement_units.name_ar', 'like', "%$request->search%")
                                ->orWhere('measurement_units.name_en', 'like', "%$request->search%");
                            })
                            ->orWhereRelation('mainMeasurementUnit', function ($q) use ($request) {
                                $q->where('measurement_units.name_ar', 'like', "%$request->search%")
                                ->orWhere('measurement_units.name_en', 'like', "%$request->search%");
                            });
                    });
                });
            })

            ->where(function($q) use($request){
                if($request->filter == 'active')
                    $q->where('quantity' , '>' , 0);
                    if($request->filter == 'inactive')
                    $q->where('quantity' , 0);
            })
            ->latest()
            ->paginate(10);
        return response()->json(['products' => $products], 200);
    }
}
