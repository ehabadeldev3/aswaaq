<?php

namespace App\Http\Controllers\Api\MobileApp;

use App\Http\Controllers\Controller;
use App\Http\Resources\MobileResources\SettingResource;
use App\Models\Setting;
use App\Models\Terms;
use App\Traits\Message;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    use Message;

    public function setting (){

        $setting = Setting::first();
        return $this->sendResponse(['setting' => new SettingResource($setting)], trans('message.messageSuccessfully'));
    }


    public function terms(Request $request)
    {
        return $this->sendResponse(['terms'=> Terms::first()->terms],'' );
    }
}
