<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\FlavorRequest;
use App\Models\Flavor;
use App\Traits\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FlavorsController extends Controller
{
    use Message;

    public function __construct()
    {
        $this->middleware('permission:flavor read', ['only' => ['index']]);
        $this->middleware('permission:flavor create', ['only' => ['store']]);
        $this->middleware('permission:flavor edit', ['only' => ['update', 'edit','activationFlavor']]);
        $this->middleware('permission:flavor delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $flavors = Flavor::
            when($request->search, function ($q) use ($request) {
                return $q->where('name_ar', 'like', '%' . $request->search . '%')->orWhere('name_en', 'like', '%' . $request->search . '%');

            })
            ->latest()->paginate(10);

        return $this->sendResponse(['flavors' => $flavors], 'Data exited successfully');
    }


    public function activationFlavor(Flavor $flavor)
    {

        $flavor->update([
            "status" =>$flavor->status == 1 ? 0 : 1
        ]);

        return $this->sendResponse([], 'Data exited successfully');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FlavorRequest $request)
    {
        Flavor::create($request->validated());
        return $this->sendResponse([], 'Data exited successfully');
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Flavor $flavor)
    {
        return $this->sendResponse(['flavor' => $flavor], 'Data exited successfully');
    }


    public function update(FlavorRequest $request, Flavor $flavor)
    {
        $flavor->update($request->validated());
        return $this->sendResponse([],'Data exited successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Flavor $flavor)
    {
        $flavor->delete();
        return $this->sendResponse([], 'Deleted successfully');
    }
}
