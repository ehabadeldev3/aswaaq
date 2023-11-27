<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = ['id'];

    protected $hidden = ['best_prices'];

    protected $casts = [
        'status' => 'integer',
        'barcode' => 'integer',
        'count_unit' => 'integer',
        'main_measurement_unit_id' => 'integer',
        'sub_measurement_unit_id' => 'integer',
        'flavor_id' => 'integer',
        'size_id' => 'integer',
    ];

    protected $appends = [
        'name', 'description', 'image_path','sub_measure_name','main_measure_name','least_price','flavor_name','best_prices','size_name'
    ];

    public function related(){
        return $this->belongsToMany(Product::class,'related_products','product_id','related_id');
    }

    public function getMainMeasureNameAttribute()
    {
        return $this->mainMeasurementUnit()->withTrashed()->first()->name;
    }
    public function getFlavorNameAttribute()
    {
        return $this->flavor()->withTrashed()->first()? $this->flavor()->withTrashed()->first()->name : '';
    }
    public function getSizeNameAttribute()
    {
        return $this->size()->withTrashed()->first()? $this->size()->withTrashed()->first()->name : '';
    }

    public function getSubMeasureNameAttribute()
    {
        return $this->subMeasurementUnit()->withTrashed()->first()->name;
    }
    public function getNameAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->name_ar : $this->name_en;
    }
    public function getDescriptionAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->description_ar : $this->description_en;
    }

    //append img path
    public function getImagePathAttribute(): string
    {
        $file_name = $this->image;
        return asset('upload/' . $file_name);
    }

    // start relation

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    public function flavor()
    {
        return $this->belongsTo(Flavor::class, 'flavor_id');
    }

    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id');
    }


    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function  suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'products_suppliers', 'product_id', 'supplier_id');
    }

    public function mainMeasurementUnit(){
        return $this->belongsTo(MeasurementUnit::class,'main_measurement_unit_id');
    }

    public function subMeasurementUnit(){
        return $this->belongsTo(MeasurementUnit::class,'sub_measurement_unit_id');
    }


    public function prices()
    {
        return $this->hasMany(Price::class);
    }


    public function orders()
    {
        return $this->hasManyThrough(OrdersProduct::class, Price::class);
    }



    public function getLeastPriceAttribute()
    {
        $suppliers_ids = $this->return_suppliers_ids_have_this_product_in_specific_area();
        if($suppliers_ids == null)
            return null;

        $suppliers_prices = $this->prices()->where('quantity','>',0)->whereIn('supplier_id',$suppliers_ids)->get();

        // $minObject = $suppliers_prices->min(function ($item) {
        //     return min($item['main_measurement_price'], $item['main_measurement_price_after_sale']);
        // });

        $min_main_measurement_price = $suppliers_prices->min("main_measurement_price");
        $min_main_measurement_price_after_sale = $suppliers_prices->where('main_measurement_price_after_sale' , '!=' , 0)->min("main_measurement_price_after_sale");
        $min_price = $min_main_measurement_price_after_sale > 0 && $min_main_measurement_price_after_sale <= $min_main_measurement_price ? $min_main_measurement_price_after_sale : $min_main_measurement_price;
        $minObject = $this->prices()->where('quantity','>',0)->whereIn('supplier_id',$suppliers_ids)->where(function($q) use($min_price){
            $q->where('main_measurement_price_after_sale',$min_price)->orWhere('main_measurement_price',$min_price);
        })->first();

        return $minObject;
    }

    public function getBestPricesAttribute()
    {

        $suppliers_ids = $this->return_suppliers_ids_have_this_product_in_specific_area();
        if($suppliers_ids == null)
            return null;
        $suppliers_prices = $this->prices()->where('quantity','>',0)->whereIn('supplier_id',$suppliers_ids)->whereBestOffer(1)->get();

        return $suppliers_prices;
    }


    protected function return_suppliers_ids_have_this_product_in_specific_area(){
        if(!current_shop_area_id())
            return null;

        $product_suppliers_ids = $this->suppliers->pluck('id')->toArray();
        $area_suppliers_ids=SuppliersArea::select(['id','supplier_id'])->whereAreaId(current_shop_area_id())->get()->pluck('supplier_id')->toArray();

        $suppliers_ids = array_intersect($product_suppliers_ids,$area_suppliers_ids);
        return $suppliers_ids ;
    }


    public function popupAds()
    {
        return $this->hasOne(PopupAds::class);
    }

    public function company()
    {
        return  $this->belongsTo(Company::class);
    }


    public function scopeProductsHasSuppliersHasPricesByAreaId($q,$area_id,$best_price = false)
    {
        $q->where('products.status',1)
        ->whereHas('suppliers',function($q) use($area_id,$best_price) {
            $q->whereRelation('areas','areas.id',$area_id);
            $q->whereRelation('prices',function($q) use($best_price){
                $q->whereColumn([['prices.supplier_id','suppliers.id'],['prices.product_id','products.id']]);
                $q->where('quantity','>',0);
                if($best_price)
                    $q->where('best_offer',1);

            });
        });

        $q->with(['media','size','flavor']);

    }


}
