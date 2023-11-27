<?php

namespace App\Http\Resources\MobileResources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryOrSubCategoryOrCompanyResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "image_path" => $this->image_path,
        ];
    }
}
