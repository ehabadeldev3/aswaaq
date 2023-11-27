<?php

namespace App\Http\Resources\MobileResources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            "id" =>(int) $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "name_ar" => $this->name_ar,
            "name_en" => $this->name_en,
            "sub_category_id" => (int) $this->sub_category_id,
            "main_measurement_unit_id" =>(int) $this->main_measurement_unit_id,
            "sub_measurement_unit_id" =>(int) $this->sub_measurement_unit_id,
            "count_unit" => $this->count_unit,
            "image_path" => $this->image_path,
            "sub_measure_name" => $this->sub_measure_name,
            "main_measure_name" => $this->main_measure_name,
            "flavor_name" => $this->flavor_name,
            "size_name" => $this->size_name,
            "flavor" => new FlavorOrSizeResource($this->flavor),
            "size" => new FlavorOrSizeResource($this->size),
            "least_price" => new LeastPriceResource(isset($this['alternative_least_price']) ? $this['alternative_least_price'] : $this->least_price),
        ];
    }
}
