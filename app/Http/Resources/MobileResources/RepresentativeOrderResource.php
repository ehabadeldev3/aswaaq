<?php

namespace App\Http\Resources\MobileResources;

use Illuminate\Http\Resources\Json\JsonResource;

class RepresentativeOrderResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "total_amount" => $this->total_amount,
            "shipping_cost" => $this->shipping_cost,
            "delivery_date" => $this->delivery_date,
            "payment_status" => $this->payment_status,
            "payment_method" => $this->payment_method,
            "shop" => new ShopResource($this->shop),
            "user" => ['name' => $this->user->name],
            "products" => RepresentativeOrderProductsResource::collection($this->products),
        ];
    }
}
