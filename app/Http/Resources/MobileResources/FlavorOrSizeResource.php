<?php

namespace App\Http\Resources\MobileResources;

use Illuminate\Http\Resources\Json\JsonResource;

class FlavorOrSizeResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            "name" => $this->name,
            "name_ar" => $this->name_ar,
            "name_en" => $this->name_en,
        ];
    }
}
