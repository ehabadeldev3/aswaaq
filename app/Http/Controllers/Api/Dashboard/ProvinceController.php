<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\ProvinceRequest;
use App\Models\Province;
use App\Traits\Message;
use Illuminate\Http\Request;


class ProvinceController extends Controller
{
    use Message;

    public function __construct()
    {
        $this->middleware('permission:provinces read', ['only' => ['index','show']]);
        $this->middleware('permission:provinces create', ['only' => ['store']]);
        $this->middleware('permission:provinces edit', ['only' => ['update']]);
        $this->middleware('permission:provinces delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $provinces = Province::when($request->search, function ($q) use ($request) {
            return $q->where('name_ar', 'like', '%' . $request->search . '%')->orWhere('name_en', 'like', '%' . $request->search . '%');
        })->latest()->paginate(15);

        return $this->sendResponse(['provinces' => $provinces], 'Data exited successfully');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProvinceRequest $request)
    {
        Province::create($request->validated());
        return $this->sendResponse([], 'Data exited successfully');
    }

    public function edit(Province $province)
    {
        return $this->sendResponse(['province' => $province], 'Data exited successfully');
    }


    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Province $province)
    {
         return $this->sendResponse(['areas' => $province->areas], 'Data exited successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProvinceRequest $request, Province $province)
    {
        $province->update($request->validated());
        return $this->sendResponse([], 'Data exited successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Province $province)
    {
        if (count($province->areas) == 0)
        {
            $province->delete();
            return $this->sendResponse([],'Deleted successfully');
        }
        return $this->sendError('Cant delete');
    }
}
