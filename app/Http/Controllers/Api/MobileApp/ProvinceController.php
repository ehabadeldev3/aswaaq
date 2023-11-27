<?php

namespace App\Http\Controllers\Api\MobileApp;

use App\Http\Controllers\Controller;
use App\Http\Resources\MobileResources\ProvinceResource;
use App\Models\Province;
use App\Traits\Message;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    use Message;

    public function province(){
        $provinces = Province::get();
        return $this->sendResponse(['provinces' => ProvinceResource::collection($provinces)], trans('message.messageSuccessfully'));
    }
}
