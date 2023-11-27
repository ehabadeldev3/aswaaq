<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\Message;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use Message;


    public function getUser()
    {
        $user =auth()->user();
        $media = $user->media;
        return $this->sendResponse(['user' => $user, 'media' => $media], 'Data exited successfully');
    }


    public function updateUser(Request $request,User $user)
    {

        $user->update([
            "name" => $request->name,
            "email" => $request->email,
            'phone' => $request->phone
        ]);

        if($request->password)
            $user->update([
                "password" => $request->password,
            ]);
        return $this->sendResponse([], 'Data exited successfully');
    }

}
