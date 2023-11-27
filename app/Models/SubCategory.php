<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubCategory extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = ['id'];

    protected $appends = [
        'image_path',
        'name',
    ];

    protected $casts = [
        'status' => 'integer'
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


    //start raletions
    public function media()
    {
        return $this->morphOne(Media::class, 'mediable');
    }

    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function virtualStocks()
    {
        return $this->hasMany(VirtualStock::class);
    }

    public function prices()
    {
        return $this->hasMany(Price::class);
    }
}
