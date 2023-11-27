<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Price extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'product_id' => 'integer',
        'supplier_id' => 'integer',
        'best_offer' => 'integer',
        'quantity' => 'integer',
        'main_measurement_price' => 'float',
        'sub_measurement_price' => 'float',
        'main_measurement_price_after_sale' => 'float',
        'sub_measurement_price_after_sale' => 'float',
    ];

    //start raletions
    public function product()
    {
        return $this->belongsTo(Product::class,'product_id')->withTrashed();
    }

    public function category()
    {
        return $this->product->category;
    }

    public function sub_category()
    {
        return $this->product->subCategory;
    }

    public function company()
    {
        return $this->product->company;
    }
    public function logs()
    {
        return $this->hasMany(ProductLog::class,'price_id');
    }
    public function unit()
    {
        return $this->belongsTo(MeasurementUnit::class,'unit_id');
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class,'supplier_id');
    }

    public function scopeGetPricesForSupplierIfDispatcherAuthenticated($q) {
        if($dispatcher = auth()->user()->dispatcher){
            $q->where(function($q) use($dispatcher){
                $suppliers_ids = $dispatcher->suppliers->pluck('id')->toArray();
                $q->whereIn('prices.supplier_id',$suppliers_ids);
            });
        }

    }




}
