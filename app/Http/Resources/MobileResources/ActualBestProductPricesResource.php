<?php

namespace App\Http\Resources\MobileResources;

use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class ActualBestProductPricesResource extends JsonResource
{

    public function toArray($request)
    {
        $product = Product::find($this['id']);
        return [
            "id" =>(int) $this['id'],
            "name" => $product->name,
            "description" => $product->description,
            "name_ar" => $product->name_ar,
            "name_en" => $product->name_en,
            "sub_category_id" => (int) $product->sub_category_id,
            "main_measurement_unit_id" =>(int) $this['main_measurement_unit_id'],
            "sub_measurement_unit_id" =>(int) $this['sub_measurement_unit_id'],
            "count_unit" => $product->count_unit,
            "image_path" => $product->image_path,
            "sub_measure_name" => $this['sub_measure_name'],
            "main_measure_name" => $this['main_measure_name'],
            "flavor_name" => $this['flavor_name'],
            "size_name" => $this['size_name'],
            "flavor" => new FlavorOrSizeResource($product->flavor),
            "size" => new FlavorOrSizeResource($product->size),
            "least_price" => new LeastPriceResource($this['least_price']),
        ];
    }
}
