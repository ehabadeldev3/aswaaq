<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'active' => 'integer'
    ];

    protected $appends = [
        'name',
    ];
    public function getNameAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->name_ar : $this->name_en;
    }

    public function employees(){
        return $this->hasMany(Employee::class);
    }

}
