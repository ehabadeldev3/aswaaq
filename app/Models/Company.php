<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['sub_category'];

    protected $casts = [
        'status' => 'integer'
    ];

    //============== appends paths ===========

    protected $appends = [
        'name',
        'image_path',
        'sub_category'
    ];

    //append img path

    public function getImagePathAttribute(): string
    {
        $file_name = $this->media->file_name;
        return asset('upload/'.$file_name);
    }

    public function getNameAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->name_ar : $this->name_en;
    }

    //append sub category

    public function getSubCategoryAttribute()
    {
        return $this->products()->get()->first()->subCategory;
    }

    //start raletions

    public function media()
    {
        return $this->morphOne(Media::class,'mediable');
    }

    public function products(){
        return $this->hasMany(Product::class);
    }

    public function earnedDiscounts(){
        return $this->hasMany(EarnedDiscount::class);
    }


    //start scopes
    public function scopeWhHasSellingMethod($q,$selling_method)
    {
        $q->whereHas('selling_method',function ($q) use($selling_method){
            $q->where('status',1);
            $q->where('selling_methods.id',$selling_method);
        });
    }
    public function scopeWhHasProductPrice($q,$selling_method)
    {
        $q->whereHas('productPrice',function ($q) use($selling_method){
            $q->where('active',1);
            $q->where('selling_method_id',$selling_method);
        });
    }
    public function scopeWhHasStoreProducts($q,$store_id)
    {
        $q->whereHas('storeProducts',function ($q) use ($store_id){
            $q->where('store_id',$store_id);
            $q->where('sub_quantity_order','>=',1);
        });
    }
}
