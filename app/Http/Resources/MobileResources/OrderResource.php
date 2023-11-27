<?php

namespace App\Http\Resources\MobileResources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "subtotal" => $this->subtotal,
            "total_amount" => $this->total_amount,
            "shipping_cost" => $this->shipping_cost,
            "hold" => $this->hold,
            "delivery_date" => $this->delivery_date,
            "order_status" => $this->order_status,
            "payment_status" => $this->payment_status,
            "used_points" => $this->used_points,
            "coupon_discount" => $this->coupon_discount,
            "coupon" => $this->coupon,
            "tax_amount" => $this->tax_amount,
            "products" => OrderProductsResource::collection($this->products),
        ];
    }
}
