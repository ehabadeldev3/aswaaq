<?php

namespace App\Http\Resources\MobileResources;

use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            "url_file" => $this->url_file,
        ];
    }
}
