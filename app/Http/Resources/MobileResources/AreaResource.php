<?php

namespace App\Http\Resources\MobileResources;

use Illuminate\Http\Resources\Json\JsonResource;

class AreaResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "shipping_price" => $this->shipping_price,
        ];
    }
}
