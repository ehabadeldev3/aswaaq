<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDiscount extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function coupon(){
        return $this->belongsTo(Discount::class,'discount_id');
    }


    public function order(){
        return $this->belongsTo(OrdersGroup::class,'order_group_id');
    }

}
