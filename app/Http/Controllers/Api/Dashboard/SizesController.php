<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\SizeRequest;
use App\Models\Size;
use App\Traits\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SizesController extends Controller
{
    use Message;

    public function __construct()
    {
        $this->middleware('permission:size read', ['only' => ['index']]);
        $this->middleware('permission:size create', ['only' => ['store']]);
        $this->middleware('permission:size edit', ['only' => ['update', 'edit','activationSize']]);
        $this->middleware('permission:size delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sizes = Size::
        when($request->search, function ($q) use ($request) {
            return $q->where('name_ar', 'like', '%' . $request->search . '%')->orWhere('name_en', 'like', '%' . $request->search . '%');
        })->latest()->paginate(10);
        return $this->sendResponse(['sizes' => $sizes], 'Data exited successfully');
    }


    public function activationSize(Size $size)
    {
        $size->update([
            "status" => $size->status == 1 ? 0 :1
        ]);
        return $this->sendResponse([], 'Data exited successfully');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SizeRequest $request)
    {
        Size::create($request->validated());
        return $this->sendResponse([], 'Data exited successfully');
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Size $size)
    {
        return $this->sendResponse(['size' => $size], 'Data exited successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SizeRequest $request, Size $size)
    {
        $size->update($request->validated());
        return $this->sendResponse([],'Data exited successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Size $size)
    {
        $size->delete();
        return $this->sendResponse([], 'Deleted successfully');
    }
}
