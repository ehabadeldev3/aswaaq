<?php

namespace App\Http\Resources\MobileResources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaxResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "status" => $this->status,
            "percentage" => $this->percentage,
        ];
    }
}
