<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdersGroup extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $appends=['area_name'];

    protected $casts = [
        'total_amount' => 'float',
        'subtotal' => 'float',
        'coupon_discount' => 'float',
        'tax_amount' => 'float',
        'refund_amount' => 'float',
        'shipping_cost' => 'integer',
        'used_points' => 'integer',
    ];


    public function orderTax()
    {
        return $this->hasMany(OrderTax::class,'order_group_id');
    }

    public function orderDiscount()
    {
        return $this->hasOne(OrderDiscount::class,'order_group_id');
    }

    public function orders(){
        return $this->hasMany(Order::class,'group_id');
    }

    public function shop(){
        return $this->belongsTo(Shop::class,'shop_id');
    }
    public function area(){
        return $this->belongsTo(Area::class);
    }
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function getAreaNameAttribute($value)
    {
        $area_with_trashed =$this->area()->withTrashed()->first();
        $provience_with_trashed =$area_with_trashed->province()->withTrashed()->first();
        return $area_with_trashed ? $area_with_trashed->name  . ( $provience_with_trashed ? ' / ' .$provience_with_trashed->name :''): '';
    }

    public function getCreatedAtAttribute($value){
        return Carbon::parse($value)->format("Y-m-d");
    }
}
