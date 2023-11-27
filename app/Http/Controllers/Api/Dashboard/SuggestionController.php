<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\SuggestionRequest;
use App\Models\Suggestion;
use App\Traits\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SuggestionController extends Controller
{
    use Message;
    public function __construct()
    {
        $this->middleware('permission:Suggestions read', ['only' => ['index']]);
        $this->middleware('permission:Suggestions create', ['only' => ['store']]);
        $this->middleware('permission:Suggestions edit', ['only' => ['update', 'edit']]);
        $this->middleware('permission:Suggestions delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $suggestions = Suggestion::when($request->search, function ($q) use ($request) {
            return $q->where('name_ar', 'like', '%' . $request->search . '%')->orWhere('name_en', 'like', '%' . $request->search . '%');
        })->latest()->paginate(15);

        return $this->sendResponse(['suggestions' => $suggestions], 'Data exited successfully');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(SuggestionRequest $request)
    {
        Suggestion::create($request->validated());
        return $this->sendResponse([], 'Data exited successfully');
    }

    public function edit(Suggestion $suggestion)
    {
        return $this->sendResponse(['suggestion' => $suggestion], 'Data exited successfully');
    }

    public function update(SuggestionRequest $request, Suggestion $suggestion)
    {
        $suggestion->update($request->validated());
        return $this->sendResponse([], 'Data exited successfully');
    }


    public function destroy(Suggestion $suggestion)
    {
        if (count($suggestion->suggestionUser) == 0)
        {
            $suggestion->delete();
            return $this->sendResponse([],'Deleted successfully');
        }
        return $this->sendError('Cant delete');
    }
}
