<?php

namespace App\Http\Controllers\Api\MobileApp;

use App\Http\Controllers\Controller;
use App\Http\Resources\MobileResources\ActualBestProductPricesResource;
use App\Http\Resources\MobileResources\ManaulBestProductPricesResource;
use App\Http\Resources\MobileResources\MediaResource;
use App\Http\Resources\MobileResources\OfferResource;
use App\Http\Resources\MobileResources\ProductResource;
use App\Models\Product;
use App\Models\Offer;
use App\Models\Price;
use App\Models\Setting;
use App\Models\SuppliersArea;
use App\Traits\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    use Message;

    public $current_shop_area_id;
    public function __construct()
    {
        $this->current_shop_area_id = current_shop_area_id();
    }

    public function productCompany($id){
        $products = Product::where([
            ['status',1],
            ['company_id',$id],
        ])
        ->productsHasSuppliersHasPricesByAreaId($this->current_shop_area_id)
        ->latest()->paginate(15);
        $data = array_merge(['data' => ProductResource::collection($products->items())],getPaginates($products));

        return $this->sendResponse(['products' => $data], trans('message.messageSuccessfully'));
    }

    public function productCompanySubCategory($subCategory_id,$company_id){

        if ($company_id > 0)
        {
            $products = Product::where([
                ['status',1],
                ['company_id',$company_id],
                ['sub_category_id',$subCategory_id],
            ])
            ->productsHasSuppliersHasPricesByAreaId($this->current_shop_area_id)
            ->latest()->paginate(10);
        }else{
            $products = Product::where([
                ['status',1],
                ['sub_category_id',$subCategory_id],
            ])
            ->productsHasSuppliersHasPricesByAreaId($this->current_shop_area_id)
            ->latest()->paginate(10);
        }
        $data = array_merge(['data' => ProductResource::collection($products->items())],getPaginates($products));

        return $this->sendResponse(['products' => $data], trans('message.messageSuccessfully'));
    }

    public function products(Request $request){
        $products = Product::where('status',1)->productsHasSuppliersHasPricesByAreaId($this->current_shop_area_id)
        ->where(function ($q) use($request){
            $q->when($request->search,function ($q) use ($request){
                return $q->where('name_ar','like',"%". $request->search ."%")
                    ->orWhere('name_en','like',"%". $request->search ."%")
                    ->orWhereRelation('flavor',function($q) use($request) {
                        $q->where('name_en','like',"%". $request->search ."%");
                        $q->orWhere('name_ar','like',"%". $request->search ."%");
                    })
                    ->orWhereRelation('size',function($q) use($request) {
                        $q->where('name_en','like',"%". $request->search ."%");
                        $q->orWhere('name_ar','like',"%". $request->search ."%");
                    })
                    ->orWhere('barcode','like',"%". $request->search ."%");
            });
        })->latest("products.created_at")->paginate($request->paginate ?? 15);

        $data = array_merge(['data' => ProductResource::collection($products->items())],getPaginates($products));

        return $this->sendResponse(['products' => $data], trans('message.messageSuccessfully'));
    }

    public function products_by_category(Request $request){
        $products = Product::
        where([
            ['status',1],
            ['category_id',$request->category_id],
        ])
        ->productsHasSuppliersHasPricesByAreaId($this->current_shop_area_id)
        ->latest("products.created_at")->paginate(15);

        $data = array_merge(['data' => ProductResource::collection($products->items())],getPaginates($products));

        return $this->sendResponse(['products' => $data], trans('message.messageSuccessfully'));
    }


    public function best_products(){

        $best_prices_limits = Setting::first()->best_prices_limits;

        $manualBestSellerProducts = collect($this->get_manual_best_product_prices($best_prices_limits))->collapse();

        $actualBestSellerProducts = ActualBestProductPricesResource::collection($this->get_actual_best_product_prices($manualBestSellerProducts,$best_prices_limits));

        $products = $manualBestSellerProducts->concat($actualBestSellerProducts)->unique(function ($item) {
            return $item['id'].$item['least_price']['id'];
        })->take($best_prices_limits)->values();

        $data = ['data' => $products];

        return $this->sendResponse(['products' => $data ], trans('message.messageSuccessfully'));
    }


    protected function get_manual_best_product_prices($best_prices_limits){
        return ManaulBestProductPricesResource::collection(Product::productsHasSuppliersHasPricesByAreaId($this->current_shop_area_id,true)
        ->inRandomOrder()
        ->take($best_prices_limits)->get());
    }

    protected function get_actual_best_product_prices($manualBestSellerProducts,$best_prices_limits){
        $diff_in_count = $best_prices_limits -  $manualBestSellerProducts->count();

        $area_suppliers_ids=SuppliersArea::select(['id','supplier_id'])->whereAreaId($this->current_shop_area_id)->get()->pluck('supplier_id')->toArray();

        return  Product::join('prices', 'prices.product_id', 'products.id')->join('products_suppliers', 'products_suppliers.product_id', 'products.id')
        ->join('suppliers', 'products_suppliers.supplier_id', 'suppliers.id')
        ->productsHasSuppliersHasPricesByAreaId($this->current_shop_area_id)
        ->select('products.id','products.main_measurement_unit_id','products.sub_measurement_unit_id','products.size_id','products.flavor_id','prices.id as price_id','prices.quantity')
        ->selectRaw('(SELECT SUM(CASE WHEN measurement_type = "main" THEN products.count_unit * orders_products.quantity ELSE orders_products.quantity END) FROM orders_products INNER JOIN orders ON orders.id = orders_products.order_id WHERE orders_products.price_id = prices.id AND orders.order_status IN ("Pending", "Shipping", "Processing", "Completed", "Partial Return")) AS orders_sold_quantity')
        ->whereIn('suppliers.id',$area_suppliers_ids)->whereColumn([['products_suppliers.supplier_id','suppliers.id'],['prices.supplier_id','suppliers.id'],['prices.product_id','products.id']])
        ->where('prices.quantity','>',0)
        ->groupBy('products.id','products.count_unit','products.main_measurement_unit_id','products.sub_measurement_unit_id','products.size_id','products.flavor_id','prices.id','prices.quantity')->orderByDesc('orders_sold_quantity')
        ->take($diff_in_count)->get()->map(function($item){
            $item = collect($item)->merge(['least_price' => Price::findOrFail($item->price_id)]);
            return $item;
        });;
    }



    public function similarProducts(Request $request){

        $product = Product::find($request->product_id);
        $first_5_characters = substr($product->name, 0, 5);

        $products = Product::with('size','flavor','mainMeasurementUnit','subMeasurementUnit')
        ->where(function($q) use($first_5_characters,$product){
            $q->where('products.name_ar','like',"%$first_5_characters%")->
            orWhere('products.name_en','like',"%$first_5_characters%")
            ->orWhere('products.flavor_id',$product->flavor_id)
            ->orWhere('products.size_id',$product->size_id);
        })->where(function($q) use($request){
            $q->where([
                ['status',1],
                ['sub_category_id',$request->sub_category_id],
                ['id','!=',$request->product_id],
            ])
            ->productsHasSuppliersHasPricesByAreaId($this->current_shop_area_id);
        })
        ->inRandomOrder()->paginate(3);
        $data = array_merge(['data' => ProductResource::collection($products->items())],getPaginates($products));

        return $this->sendResponse(['products' => $data], trans('message.messageSuccessfully'));
    }
    public function product_details(Request $request,Product $product){

        $first_5_characters = substr($product->name, 0, 5);
        $related_products = Product::with(['flavor','size'])
        ->where(function($q) use($first_5_characters){
            $q->where('products.name_ar','like',"%$first_5_characters%")->
            orWhere('products.name_en','like',"%$first_5_characters%");
        })->where(function($q) use($product){
            $q->where([
                ['status',1],
                ['company_id',$product->company_id],
                ['sub_category_id',$product->sub_category_id],
                ['id','!=',$product->product_id],
            ]);
        })
        ->productsHasSuppliersHasPricesByAreaId($this->current_shop_area_id)
        ->latest()->get();

        if($request->price_id){
            $product['alternative_least_price'] = Price::where('product_id',$product->id)->findOrFail($request->price_id);
        }
        $different_sizes_for_the_same_product = $related_products->where('size_id','!=',$product->size_id)->where('flavor_id',$product->flavor_id)->unique('size_id')->values();
        $different_flavors_for_the_same_product = $related_products->where('flavor_id','!=',$product->flavor_id)->where('size_id',$product->size_id)->unique('flavor_id')->values();
        return $this->sendResponse([
            'product' => new ProductResource($product),
            'media' => MediaResource::collection($product->media),
            'sizes' => ProductResource::collection($different_sizes_for_the_same_product),
            'flavors' => ProductResource::collection($different_flavors_for_the_same_product),
        ], trans('message.messageSuccessfully'));
    }

    public function get_products_offers(Request $request)
    {
        $offers = Offer::whereHas('product' , function($q){
            $q->productsHasSuppliersHasPricesByAreaId($this->current_shop_area_id);
        })->orWhereDoesntHave('product')
        ->with('product')->paginate(10);
        $data = array_merge(['data' => OfferResource::collection($offers->items())],getPaginates($offers));
        return $this->sendResponse($data, trans('message.messageSuccessfully'));
    }

}
