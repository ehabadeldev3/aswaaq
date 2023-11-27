<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\ClientCategoryRequest;
use App\Models\ClientCategory;
use App\Traits\Message;
use Illuminate\Http\Request;


class ClientCategoriesController extends Controller
{
    use Message;

    public function __construct()
    {
        $this->middleware('permission:client category read', ['only' => ['index']]);
        $this->middleware('permission:client category create', ['only' => ['store']]);
        $this->middleware('permission:client category edit', ['only' => ['update', 'edit']]);
        $this->middleware('permission:client category delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $client_categories = ClientCategory::
        when($request->search, function ($q) use ($request) {
            return $q->where('from_amount', 'like', '%' . $request->search . '%')->orWhere('to_amount', 'like', '%' . $request->search . '%');
        })->latest()->paginate(10);
        return $this->sendResponse(['client_categories' => $client_categories], 'Data exited successfully');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ClientCategoryRequest $request)
    {
        ClientCategory::create($request->validated());
        return $this->sendResponse([], 'Data exited successfully');
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(ClientCategory $clientCategory)
    {
        return $this->sendResponse(['client_category' => $clientCategory], 'Data exited successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ClientCategoryRequest $request, ClientCategory $clientCategory)
    {
        $clientCategory->update($request->validated());
        return $this->sendResponse([],'Data exited successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ClientCategory $clientCategory)
    {
        $clientCategory->delete();
        return $this->sendResponse([], 'Deleted successfully');
    }
}
