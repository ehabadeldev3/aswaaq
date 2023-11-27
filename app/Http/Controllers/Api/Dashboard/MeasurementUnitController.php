<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\MeasurementRequest;
use App\Models\MeasurementUnit;
use App\Traits\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MeasurementUnitController extends Controller
{
    use Message;

    public function __construct()
    {
        $this->middleware('permission:measureUnit read', ['only' => ['index']]);
        $this->middleware('permission:measureUnit create', ['only' => ['store']]);
        $this->middleware('permission:measureUnit edit', ['only' => ['update', 'edit','activationMeausre']]);
        $this->middleware('permission:measureUnit delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $measures = MeasurementUnit::
            when($request->search, function ($q) use ($request) {
                return $q->where('name_ar', 'like', '%' . $request->search . '%')->orWhere('name_en', 'like', '%' . $request->search . '%');

            })
            ->latest()->paginate(10);

        return $this->sendResponse(['measures' => $measures], 'Data exited successfully');
    }


    public function activationMeausre(MeasurementUnit $measurementUnit)
    {
        $measurementUnit->update([
                "status" => $measurementUnit->status == 1 ? 0: 1
        ]);
        return $this->sendResponse([], 'Data exited successfully');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MeasurementRequest $request)
    {
        MeasurementUnit::create($request->validated());
        return $this->sendResponse([], 'Data exited successfully');
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $measurementUnit = MeasurementUnit::findOrFail($id);
        return $this->sendResponse(['measure' => $measurementUnit], 'Data exited successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(MeasurementRequest $request, $id)
    {
        $measurementUnit = MeasurementUnit::findOrFail($id);
        $measurementUnit->update($request->valiadted());
        return $this->sendResponse([],'Data exited successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $measurementUnit = MeasurementUnit::findOrFail($id);
        $measurementUnit->delete();
        return $this->sendResponse([], 'Deleted successfully');
    }
}
