<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Car extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = ['id'];
    protected $appends = ['image_path','license_path','name', 'description'];

    public function orders() {
        return $this->hasMany(Order::class);
    }

    public function getImagePathAttribute(){
        return $this->image ? asset('upload/' . $this->image) : '';
    }

    public function getLicensePathAttribute(){
        return $this->license ? asset('upload/' . $this->license) : '';
    }


    public function getNameAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->name_ar : $this->name_en;
    }
    public function getDescriptionAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->description_ar : $this->description_en;
    }
}
