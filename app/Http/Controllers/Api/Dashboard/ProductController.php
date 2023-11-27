<?php

namespace App\Http\Controllers\Api\Dashboard;


use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\ProductRequest;
use App\Models\Category;
use App\Models\Company;
use App\Models\Flavor;
use App\Models\MeasurementUnit;
use App\Models\Media;
use App\Models\Product;
use App\Models\ProductsSupplier;
use App\Models\Size;
use App\Models\Supplier;
use App\Traits\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{

    use Message;

    public function __construct()
    {
        $this->middleware('permission:product read', ['only' => ['index','activeProduct']]);
        $this->middleware('permission:product create', ['only' => ['store']]);
        $this->middleware('permission:product edit', ['only' => ['update', 'edit','activationProduct','delete_image']]);
        $this->middleware('permission:product delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *e
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $products = Product::with('category','company','mainMeasurementUnit','subMeasurementUnit','size','flavor')
        ->where(function($q) use($request){
            $q->when($request->search,function($q) use($request) {
                $q->where('name_ar' ,'like',"%$request->search%")
                ->orWhere('name_en' ,'like',"%$request->search%");
            });
        })
        ->where(function($q) use($request){
            $q->when($request->category_id,function($q) use($request) {
                $q->where('category_id',$request->category_id);
            });
        })
        ->where(function($q) use($request){
            $q->when($request->sub_category_id,function($q) use($request) {
                $q->where('sub_category_id',$request->sub_category_id);
            });
        })
        ->where(function($q) use($request){
            $q->when($request->supplier_id,function($q) use($request) {
                $q->whereRelation('suppliers','suppliers.id',$request->supplier_id);
            });
        })->latest()->paginate( $request->pagination ?? 25 );

        return $this->sendResponse(['products' => $products], 'Data exited successfully');
    }


    /**
     * get active Product
     */
    public function activeProduct()
    {
        $products = Product::where('status', 1)->get();
        return $this->sendResponse(['products' => $products], 'Data exited successfully');
    }


    public function activationProduct(Product $product)
    {
        $product->update([
            "status" => $product->status == 1 ? 0 : 1
        ]);
        return $this->sendResponse([], 'Data exited successfully');
    }

    public function create()
    {
        $categories = Category::where('status', 1)->get();
        $companies = Company::where('status', 1)->get();
        $units = MeasurementUnit::where('status', 1)->get();
        $suppliers = Supplier::where('status', 1)->get();
        $sizes = Size::where('status', 1)->get();
        $flavors = Flavor::where('status', 1)->get();

        return $this->sendResponse([
            'categories' => $categories,
            'units' => $units,
            'suppliers' => $suppliers,
            'sizes' => $sizes,
            'flavors' => $flavors,
            'companies' => $companies,
        ], 'Data exited successfully');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {


        $data = collect($request->validated())->except(['files','image','suppliers','units'])->toArray();
        $this->store_image($data,$request->image,'image');
        $product = Product::create($data);

        if ($request->hasFile('files'))
            $this->storeImages($request->file('files'),$product,'product');

        foreach($request->suppliers as $supplier)
            ProductsSupplier::create(['supplier_id'=>$supplier , 'product_id' => $product->id]);

        // $this->associateAlternativeProducts($request,$product);
        return $this->sendResponse([], 'Data exited successfully');

    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::with('media:mediable_id,file_name,id')->find($id);
        $categories = Category::where('status', 1)->get();
        $companies = Company::where('status', 1)->get();
        $units = MeasurementUnit::where('status', 1)->get();
        $suppliers = Supplier::where('status', 1)->get();
        $sizes = Size::where('status', 1)->get();
        $flavors = Flavor::where('status', 1)->get();
        $selected_suppliers = ProductsSupplier::select(['id','supplier_id'])->where('product_id',$id)->get()->pluck('supplier_id')->toArray();
        return $this->sendResponse([
            'categories' => $categories,
            'units' => $units,
            'suppliers' => $suppliers,
            'product' => $product,
            'selected_suppliers' => $selected_suppliers,
            'sizes' => $sizes,
            'flavors' => $flavors,
            'companies' => $companies,
        ], 'Data exited successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, Product $product)
    {
        $data = collect($request->validated())->except(['files','image','suppliers','units'])->toArray();

        if ($request->hasFile('image') ) {
            $this->unlink_image($product->image);
            $this->store_image($data,$request->image,'image');
        }

        $product->update($data);


        if ($request->hasFile('files'))
            $this->storeImages($request->file('files'),$product,'product');

        $product->suppliers()->detach();
        foreach($request->suppliers as $supplier)
            ProductsSupplier::create(['supplier_id'=>$supplier , 'product_id' => $product->id]);

        // $this->associateAlternativeProducts($request,$product);


        return $this->sendResponse([], 'Data exited successfully');

    }


    public function destroy(Product $product)
    {
        $this->unlink_image($product->image);

        foreach ($product->media as $item) {
            $this->unlink_image($item->file_name);
            $item->delete();
        }

        $product->delete();
        return $this->sendResponse([], 'Deleted successfully');

    }

    protected function storeImages($request_files,$model,$folder){
        $i = 0;
        if ($request_files) {
            foreach ($request_files as $index => $file) {
                saveFile($file,$model,$folder);
                $i++;
            }
        }
    }



    public function delete_image(Request $request, Product $product)
    {

        $media = Media::find($request->idMedia);

        $this->unlink_image($media->file_name);

        $media->delete();
        return $this->sendResponse([], 'Deleted successfully');

    }


    protected function store_image(&$data,$file,$attribute)
    {
        $image = time() . '.' .$file->getClientOriginalName();
        // picture move
        $file->storeAs('product', $image, 'general');
        $data[$attribute] = 'product/'.$image;
    }

    protected function unlink_image($image_path)
    {
        if (File::exists('upload/' . $image_path) && $image_path) {
            unlink('upload/' . $image_path);
        }
    }
}
