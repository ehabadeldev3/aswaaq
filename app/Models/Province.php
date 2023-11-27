<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Province extends Model
{
    use HasFactory,SoftDeletes;
    protected $guarded = ['id'];
    protected $appends = [
        'name',
    ];

    public function getNameAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->name_ar : $this->name_en;
    }
    public function areas(){
        return $this->hasMany(Area::class);
    }

    public function users(){
        return $this->hasMany(User::class);
    }
}
