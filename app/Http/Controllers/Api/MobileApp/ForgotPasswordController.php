<?php

namespace App\Http\Controllers\Api\MobileApp;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    use Message;

    public function forgotPassword (Request $request){
        $request->validate([
            'phone' => 'required|string|min:11|exists:users,phone',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('phone',$request->phone)->first();
        $user->update([
            'password' => $request->input('password'),
        ]);

        return $this->sendResponse([], trans('message.messageSuccessfully'));
    }
}
