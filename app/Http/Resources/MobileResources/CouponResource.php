<?php

namespace App\Http\Resources\MobileResources;

use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            "type" => $this->type,
            "value" => $this->value,
            "code" => $this->code,
            "greater_than" => $this->greater_than,
        ];
    }
}
