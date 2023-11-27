<?php

namespace App\Http\Resources\MobileResources;

use Illuminate\Http\Resources\Json\JsonResource;

class RepresentativeOrderProductsResource extends JsonResource
{

    public function toArray($request)
    {
        $product = $this->product;
        return [
            "id" => $this->id,
            "product" => array_merge($this->returnNameArAndNameEnArray($product),[
                "size" => $this->returnNameArAndNameEnArray($product->size),
                "flavor" => $product->flavor ? $this->returnNameArAndNameEnArray($product->flavor) : null,
                "subMeasurementUnit" => $this->returnNameArAndNameEnArray($product->subMeasurementUnit),
                "mainMeasurementUnit" => $this->returnNameArAndNameEnArray($product->mainMeasurementUnit),
            ]),
            "pivot" => new OrderProductsPivotResource($this->pivot),
        ];
    }


    protected function returnNameArAndNameEnArray($model){
        return [
            'name_ar' => $model->name_ar,
            'name_en' => $model->name_en
        ];
    }
}
