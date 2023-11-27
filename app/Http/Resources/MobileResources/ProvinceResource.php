<?php

namespace App\Http\Resources\MobileResources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProvinceResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "areas" => AreaResource::collection($this->areas)
        ];
    }
}
