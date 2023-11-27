<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Terms;
use App\Traits\Message;
use Illuminate\Http\Request;

class TermsAndConditionsController extends Controller
{
    use Message;

    public function __construct()
    {
        $this->middleware('permission:Terms And Conditions', ['only' => ['index','store']]);
    }


    public function index(Request $request)
    {
        return response()->json(Terms::first());
    }


    public function store(Request $request)
    {
        Terms::first()->update(['terms_ar' => $request->terms_ar,'terms_en' => $request->terms_en]);
        return $this->sendResponse([],'Data exited successfully');
    }

}
