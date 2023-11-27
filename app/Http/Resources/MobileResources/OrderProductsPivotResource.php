<?php

namespace App\Http\Resources\MobileResources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductsPivotResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'measurement_type' => $this->measurement_type,
            'measurement_id' => (int) $this->measurement_id,
            'order_id' => (int) $this->order_id,
            'price_id' => (int) $this->price_id,
            'quantity' => (int) $this->quantity,
            'price' => $this->price,
            'sub_total' => $this->sub_total
        ];
    }
}
