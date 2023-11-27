<?php

namespace App\Http\Resources\MobileResources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShopResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            "id" => (int) $this->id,
            "name" => $this->name,
            "geolocation" => $this->geolocation,
            "address" => $this->address,
            "feature_sign" => $this->feature_sign,
            "phone" => $this->phone,
            "land_line" => $this->land_line,
            "province_id" => (int) $this->province_id,
            "area_id" => (int) $this->area_id,
            "media" => MediaResource::collection($this->media),
            "area" => ['name' =>$this->area->name],
            "province" => ['name' =>$this->province->name],
        ];
    }
}
