<?php

namespace App\Http\Resources\MobileResources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductsResource extends JsonResource
{

    public function toArray($request)
    {
        $product = $this->product;
        return [
            "product" => [
                "name" => $product->name,
                "image_path" => $product->image_path,
                "flavor_name" => $product->flavor_name,
                "size_name" => $product->size_name,
            ],
            "pivot" => ['quantity' => (int) $this->pivot->quantity, 'price' => $this->pivot->price,'sub_total' => $this->pivot->sub_total],
        ];
    }
}
