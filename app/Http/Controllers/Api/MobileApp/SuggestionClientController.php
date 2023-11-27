<?php

namespace App\Http\Controllers\Api\MobileApp;

use App\Http\Controllers\Controller;
use App\Http\Resources\MobileResources\SuggestionResource;
use App\Models\Suggestion;
use App\Models\SuggestionUser;
use App\Models\User;
use App\Notifications\Admin\AddNotification;
use App\Traits\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SuggestionClientController extends Controller
{
    use Message;

    public function getSuggestion(){
        $suggestions = Suggestion::all();
        return $this->sendResponse(['suggestions' => SuggestionResource::collection($suggestions)], trans('message.messageSuccessfully'));
    }

    public function suggestion(Request $request){
        $request->validate([
            'suggestion_id' => 'required|integer|exists:suggestions,id',
            'notes' => 'required|string|min:5',
        ]);

        $data = SuggestionUser::create([
            'suggestion_id'=> $request->suggestion_id,
            'notes'=> $request->notes,
            'user_id'=> auth()->id(),
        ]);

        $id = $data->id;

        User::whereAuthId(1)
            ->whereRelation('roles.notify','name','Add Suggestion')
            ->each(function ($admin) use($id){
                $admin->notify(new AddNotification('',$id,'showSuggestionClient'," يوجد اقتراح او شكوى من العميل " .auth()->user()->name,auth()->user()->image_path,"There is a new suggestion or complaint from the customer" .auth()->user()->name));
            });
        auth()->user()->notify(new AddNotification('',$id,'showSuggestionClient',"تم انشاء الشكوى بنجاح",auth()->user()->image_path,"The complaint has been created successfully"));
        return $this->sendResponse([], trans('message.messageSuccessfully'));
    }
}
