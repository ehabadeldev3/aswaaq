<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrdersRunSheet;
use App\Models\RunSheet;
use App\Models\RunSheetProducts;
use App\Traits\Message;
use Illuminate\Http\Request;

class RunSheetController extends Controller
{
    use Message;

    public function __construct()
    {
        $this->middleware('permission:run sheet read', ['only' => ['index']]);
        $this->middleware('permission:run sheet edit', ['only' => ['create_run_sheet','updateDeficitForSheetItem']]);
    }

    public function index(Request $request)
    {
        $sheets = RunSheet::whereHas('orders')->withCount('orders')->with('products','products.product','products.measurement_unit')->when($request->date,function($q) use($request) {
            $q->whereDate('created_at',$request->date);
        })->latest()->paginate(25);
        return $this->sendResponse(['sheets' => $sheets], 'Data exited successfully');
    }

    public function create_run_sheet(Request $request){
        $request->validate(['orders' => 'required|array|min:1','orders.*' => 'exists:orders,id',]);
        $orders = Order::with(['products','products.product','products.product.size','products.product.flavor'])
        ->whereOrderStatus('Pending')
        ->getSupplierOrdersIfDispatcherAuthenticated()->find($request->orders);
        $run_sheet = $this->create_sheet_and_assign_order_to_it($orders);
        $orders = $orders->flatMap(function($item){
            return $item->products->map(function($sub_item){
                return [
                    'product_id' => $sub_item->product_id,
                    'product' => $sub_item->product->name,
                    'flavor' => $sub_item->product->flavor->name,
                    'size' => $sub_item->product->size->name,
                    'measurement_id' => $sub_item->pivot->measurement_id,
                    'quantity' => $sub_item->pivot->quantity,
                    'unit_price' => "".$sub_item->pivot->price,
                    'sub_total' => $sub_item->pivot->sub_total,
                    'measurement_type' => $sub_item->pivot->measurement_id == $sub_item->product->sub_measurement_unit_id ? $sub_item->product->sub_measure_name: $sub_item->product->main_measure_name,
                ];
            });
        })->groupBy(['product_id','measurement_id','unit_price'])->collapse()->values()
        ->flatMap(function($item) use($run_sheet){
            return $item->map(function($sub_item) use($run_sheet){

                $first_item = $sub_item->first();
                $total_quantity = $sub_item->sum('quantity');
                $this->assign_sheet_with_products($run_sheet ,$first_item, $total_quantity);

                return [
                    'product' => $first_item['product'],
                    'flavor' => $first_item['flavor'],
                    'size' => $first_item['size'],
                    'measurement type' => $first_item['measurement_type'],
                    'quantity' => $total_quantity,
                    'unit price' => $first_item['unit_price'],
                    'total amount' => $sub_item->sum('sub_total'),
                ];
            });
        })->values();

        
        return $orders;
    }


    public function updateDeficitForSheetItem(Request $request){
        $product_run_sheet = RunSheetProducts::findOrFail($request->run_sheet_product_id);
        $request->validate(['deficit' => 'required|numeric|gte:0|lte:'.$product_run_sheet->quantity]);
        $product_run_sheet->update(['deficit' => $request->deficit]);
        return response()->json([],200);
    }


    protected function create_sheet_and_assign_order_to_it($orders , $sheet = null){
        if($orders->count() > 0){
            $sheet = RunSheet::create();
            foreach($orders as $order){
                $this->remove_quantity_if_the_order_exists_in_old_sheet($order);
            }
            $sheet->orders()->syncWithoutDetaching($orders);
        }
        return $sheet;
    }


    protected function assign_sheet_with_products($sheet,$product,$sum_quantities){
        RunSheetProducts::create([
            'run_sheet_id' => $sheet->id,
            'product_id' => $product['product_id'],
            'measurement_id' => $product['measurement_id'],
            'unit_price' => $product['unit_price'],
            'quantity' => $sum_quantities,
        ]);
    }
    protected function remove_quantity_if_the_order_exists_in_old_sheet($order){
        $order_in_old_sheet = OrdersRunSheet::where('order_id',$order->id)->first();
        if($order_in_old_sheet){
            foreach($order->products as $product){
                $old_run_sheet_product = RunSheetProducts::where('run_sheet_id',$order_in_old_sheet->run_sheet_id)
                ->where('product_id',$product->product_id)
                ->where('measurement_id',$product->pivot->measurement_id)
                ->where('unit_price',$product->pivot->price)->first();
                $old_run_sheet_product->quantity - $product->pivot->quantity == 0 ? $old_run_sheet_product->delete() :
                $old_run_sheet_product->update(['quantity' => $old_run_sheet_product->quantity - $product->pivot->quantity]);
            }
            $order_in_old_sheet->delete();
        }
    }

    



}
