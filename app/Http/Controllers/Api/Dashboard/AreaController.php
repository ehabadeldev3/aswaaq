<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\AreaRequest;
use App\Models\Area;
use App\Models\Province;
use App\Traits\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AreaController extends Controller
{
    use Message;

    public function __construct()
    {
        $this->middleware('permission:provinces read', ['only' => ['index','show','get_all_areas']]);
        $this->middleware('permission:provinces create', ['only' => ['store','create']]);
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
        $areas = Area::with('province')->when($request->search,function ($q) use($request){
                return $q->where('name_ar','like','%'.$request->search.'%')->orWhere('name_en','like','%'.$request->search.'%')
                    ->orWhereRelation('province',function($q) use($request){
                        $q->where('name_ar','like','%'.$request->search.'%')->orWhere('name_en','like','%'.$request->search.'%');
                    });
            })->latest()->paginate(10);

        return $this->sendResponse(['areas' => $areas],'Data exited successfully');
    }


    public function get_all_areas(Request $request)
    {
        $areas = Area::with('province')->latest()->get();
        return $this->sendResponse(['areas' => $areas],'Data exited successfully');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $provinces = Province::all();

        return $this->sendResponse(['provinces' => $provinces], 'Data exited successfully');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AreaRequest $request)
    {
        Area::create($request->validated());
        return $this->sendResponse([],'Data exited successfully');
    }


    public function edit(Area $area)
    {
        $provinces = Province::all();
        return $this->sendResponse(['area'=>$area,'provinces'=>$provinces],'Data exited successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AreaRequest $request, Area $area)
    {
        $area->update($request->validated());
        return $this->sendResponse([],'Data exited successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Area $area)
    {
        $area->delete();
        return $this->sendResponse([],'Deleted successfully');
    }
}
