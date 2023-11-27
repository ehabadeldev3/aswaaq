<?php

namespace App\Http\Controllers\Api\MobileApp;

use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\UserResource;
use App\Models\Shop;
use App\Models\User;
use App\Notifications\Admin\AddNotification;
use App\Traits\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthMobileController extends Controller
{
    use Message;

    public function signUp(Request $request){

        $data = $request->validate([
            'name' => 'required|string|min:3|max:190',
            'firebase_token' => 'required|string',
            'phone' => 'required|string|min:11|unique:users,phone',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $data['code'] = "+02";
        $data['auth_id'] = 2;
        $data['role_name'] = ["client"];
        $data['status'] = 0;

        $user = User::create($data);
        $user->client()->create($data);
        $this->send_notification_after_registeration($user);
        return $this->sendResponse([],trans('message.waitActive'));
    }


    public function signIn(Request $request){
        // Validator request
        $request->validate([
            'phone' => 'required|string|min:11|exists:users,phone',
            'password' => 'required|string|min:8',
            'firebase_token' => 'required|string',
        ]);

        //start access token
        $credentials = $request->only("phone", "password");
        if ($token = Auth::guard('api')->attempt($credentials)) {

            $user = Auth::guard('api')->user();

            if($user->status == 1  && $user->auth_id == 2){
                $client = $user->client;
                $user->client()->update([
                    'firebase_token' => $request->firebase_token,
                ]);
                return  $this->sendResponse(array_merge($this->respondWithToken($token),['check_shop' => $client->shops()->count() > 0]),trans('message.messageSuccessfully'));

            }else{
                return $this->sendError(trans('message.NotActive'));
            }

        }else{

            return $this->sendError(trans('message.Please check your phone number and password'));
        }
    }

    public function me(){
        $user_id = \auth()->user()->id;
        $user=User::with('client')->find($user_id);
        $user['image_path'] = $user->media ? asset('upload/user/'.$user->media->file_name) : '';
        return $this->sendResponse(['user' => $user], trans('message.messageSuccessfully'));
    }

    // create token
    protected function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => new UserResource(auth()->guard('api')->user()),
            'permission' =>  PermissionResource::collection((auth()->guard('api')->user()->getAllPermissions()))
        ];

    }//**********end respondWithToken************/

    /**
     * change Password User
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' =>'required|string|min:8',
            'password' => 'required|string|min:8|confirmed',
        ]);
        $user = \auth()->user();
        if (Hash::check($request->old_password, $user->password))
        {
            $user->update([
                'password' => $request->input('password'),
            ]);
            return $this->sendResponse([], trans('message.messageSuccessfully'));
        }
        return $this->sendError(trans('message.sorry the old password is not correct'));
    }

    /**
     * check Phone User
     */

    public function checkPhone($phone){
        $user = User::where('phone',$phone)->first();
        if ($user == null){
            return $this->sendError(trans('message.messageError'));
        }
        return $this->sendResponse([], trans('message.messageSuccessfully'));
    }

    protected function send_notification_after_registeration($user){
        User::whereAuthId(1)
        ->whereRelation('roles.notify','name','Add Client')
        ->each(function ($admin) use($user){
            $admin->notify(new AddNotification('',$user->id,'client_profile', " تم تسجيل عميل جديد باسم " .$user->name ,$user->image_path," New client has been registered ( " .$user->name . " )"));
        });
        $user->notify(new AddNotification('',$user->id,'client_profile','لقد تم تسجيل الحساب بنجاح',$user->image_path,'Your Account has been registered successfully'));
    }

}
