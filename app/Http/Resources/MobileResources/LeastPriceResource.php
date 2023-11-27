<?php

namespace App\Http\Resources\MobileResources;

use Illuminate\Http\Resources\Json\JsonResource;

class LeastPriceResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "quantity" => $this->quantity,
            "main_measurement_price" => $this->main_measurement_price,
            "main_measurement_price_after_sale" => $this->main_measurement_price_after_sale,
            "sub_measurement_price" => $this->sub_measurement_price,
            "sub_measurement_price_after_sale" => $this->sub_measurement_price_after_sale,
        ];
    }
}
