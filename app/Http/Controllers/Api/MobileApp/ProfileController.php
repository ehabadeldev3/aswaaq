<?php

namespace App\Http\Controllers\Api\MobileApp;

use App\Http\Controllers\Controller;
use App\Traits\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    use Message;

    public function updateProfile (Request $request){
        $v = Validator::make($request->all(),[
            'name' => 'required|string|min:3|max:190',
            'phone' => 'required|string|min:11|unique:users,phone,'.auth()->user()->id,
            // 'trade_name' => 'required|string|min:3|max:190',
            // 'address' => 'required|string|min:3|max:300',
            // 'location' => 'required|string|min:3|max:300',
            // 'province_id' => 'required|integer|exists:provinces,id',
            // 'area_id' => 'required|integer|exists:areas,id',
        ]);



        if($v->fails()) {
            return $this->sendError(trans('message.messageError'), $v->errors());
        }

        $user = auth()->user();
        $user->update($request->all());

        saveFile($request->image,$user,'user','update');

        return $this->sendResponse($user,trans('message.messageSuccessfully'));
    }


    public function update_image(Request $request){
        $request->validate(['image' => 'required' . ($request->hasFile('image') ? '|mimes:jpeg,jpg,png,gif|max:10000' : '')]);
        $user = auth()->user();
        saveFile($request->image,$user,'user','update');
        return $this->sendResponse($user,trans('message.messageSuccessfully'));
    }

    public function get_all_notifications(Request $request)
    {
        $Notifications = auth()->guard('api')->user()->notifications()->latest()->paginate(15);
        $NotificationsCount = auth()->guard('api')->user()->unreadNotifications->count();
        return $this->sendResponse(['Notifications' => $Notifications,'count' => $NotificationsCount], 'Data exited successfully');
    }

    public function get_un_read_notifications()
    {
        $user = auth()->guard('api')->user();
        $Notifications = $user->unreadNotifications;
        $NotificationsCount = $Notifications->count();
        return $this->sendResponse(['Notifications' => $Notifications,'count'=>$NotificationsCount], 'Data exited successfully');
    }

    public function mark_as_read()
    {
        $user = auth()->guard('api')->user();
        $user->unreadNotifications()->update(['read_at' => now()]);
        return $this->sendResponse([], 'Data exited successfully');
    }


}


