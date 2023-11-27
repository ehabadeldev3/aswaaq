<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Size extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = ['id'];
    protected $appends = [
        'name',
    ];
    protected $casts = [
        'status' => 'integer'
    ];


    public function getNameAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->name_ar : $this->name_en;
    }
}
