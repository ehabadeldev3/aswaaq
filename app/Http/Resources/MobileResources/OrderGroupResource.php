<?php

namespace App\Http\Resources\MobileResources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderGroupResource extends JsonResource
{

    public function toArray($request)
    {
        $shop = $this->shop;
        return [
            "id" => $this->id,
            "subtotal" => $this->subtotal,
            "total_amount" => $this->total_amount,
            "shipping_cost" => $this->shipping_cost,
            "payment_method" => $this->payment_method,
            "payment_status" => $this->payment_status,
            "used_points" => $this->used_points,
            "coupon_discount" => $this->coupon_discount,
            "coupon" => $this->coupon,
            "tax_amount" => $this->tax_amount,
            "created_at" => $this->created_at,
            "orders" => OrderResource::collection($this->orders),
            "shop" => ['name' => $shop->name],
        ];
    }
}
