<?php

namespace App\Http\Resources\MobileResources;

use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            "url" => $this->url,
            "description" => $this->description,
            "image_path" => $this->image_path,
            "ex_date" => $this->ex_date,
            "product" =>$this->product ?  new ProductResource($this->product) : null,
        ];
    }
}
