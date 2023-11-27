<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdersProduct extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function measurement(){
        return $this->belongsTo(MeasurementUnit::class,'measurement_id');
    }

    public function order(){
        return $this->belongsTo(Order::class,'order_id');
    }
}
