<?php

namespace App\Http\Resources\MobileResources;

use Illuminate\Http\Resources\Json\JsonResource;

class WalletTargetResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            "amount" =>(int) $this->amount,
            "points" => (int) $this->points,
            "end_date" => $this->end_date,
            "start_date" => $this->start_date,
            "total_amount" => $this->total_amount,
        ];
    }
}
