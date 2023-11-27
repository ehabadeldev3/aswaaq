<?php

namespace App\Http\Resources\MobileResources;

use Illuminate\Http\Resources\Json\JsonResource;

class SliderAdsResource extends JsonResource
{

    public function toArray($request)
    {
        $product= $this->product;
        return [
            "url" => $this->url,
            "image_path" => $this->image_path,
            "product" =>$product ?  ['id' => $product->id,'sub_category_id' => $product->sub_category_id,'name' => $product->name] : null,
            "sub_category" =>$this->sub_category ?  new CategoryOrSubCategoryOrCompanyResource($this->sub_category) : null,
        ];
    }
}
