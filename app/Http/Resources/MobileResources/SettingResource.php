<?php

namespace App\Http\Resources\MobileResources;

use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            "phone" => $this->phone,
            "wats_app" => $this->wats_app,
            "order_amount" => $this->order_amount,
            "facebook" => $this->facebook,
            "cash_on_delivery" => $this->cash_on_delivery,
            "online_payment" => $this->online_payment,
        ];
    }
}
