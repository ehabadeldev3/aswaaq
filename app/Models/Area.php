<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
    use HasFactory,SoftDeletes;
    protected $guarded = ['id'];
    protected $appends = [
        'name',
    ];
    protected $casts = [
        'shipping_price' => 'float',
    ];
    public function getNameAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->name_ar : $this->name_en;
    }

    public function province()
    {
        return $this->belongsTo(Province::class,'province_id');
    }

    public function representatives(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Representative::class,'representative_areas','area_id','representative_id','id','id');
    }

    public function store(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Store::class,'store_areas','area_id','store_id','id','id');
    }

    public function users(){
        return $this->hasMany(User::class);
    }
}
