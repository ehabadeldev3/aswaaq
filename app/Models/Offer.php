<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $guarded = ["product_name_ar","product_name_en"];

    protected $appends = [
        'image_path',
        'description',
    ];
    protected $casts = [
        'external' => 'integer',
        'product_id' => 'integer',
    ];

    //append img path
    public function getImagePathAttribute(): string
    {
        return $this->image  ? asset('upload/'.$this->image) : '';
    }
    public function getExternalAttribute($value): string
    {
        return (int) $value;
    }
    public function getDescriptionAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->description_ar : $this->description_en;
    }

    public function setExternalAttribute($value)
    {
        $this->attributes["external"] = $value == "true" ? 1 : 0;
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
