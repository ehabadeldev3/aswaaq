<?php

namespace App\Http\Controllers\Api\MobileApp;

use App\Http\Controllers\Controller;
use App\Http\Resources\MobileResources\AreaResource;
use App\Traits\Message;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    use Message;

    public function shippingPrice(){

        $area = auth()->user()->client->selected_shop->area;

        return $this->sendResponse(['area' => new AreaResource($area)], trans('message.messageSuccessfully'));
    }
}
