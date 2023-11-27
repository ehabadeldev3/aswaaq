<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\ShiftRequest;
use App\Models\Shift;
use App\Traits\Message;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    use Message;

    public function __construct()
    {
        $this->middleware('permission:shift read', ['only' => ['index','activeShift']]);
        $this->middleware('permission:shift create', ['only' => ['store','create']]);
        $this->middleware('permission:shift edit', ['only' => ['update','edit','activationShift']]);
        $this->middleware('permission:shift delete', ['only' => ['destroy']]);
    }
    /**
     * get active Shift
     */
    public function activeShift()
    {
        $shifts = Shift::where('active', 1)->get();
        return $this->sendResponse(['shifts' => $shifts], 'Data exited successfully');
    }


    /**
     * activation Shift
     */
    public function activationShift(Shift $shift)
    {
        $shift->update([
            "active" =>$shift->active == 1? 0: 1
        ]);
        return $this->sendResponse([], 'Data exited successfully');
    }


    public function index(Request $request)
    {
        $shifts = Shift::when($request->search, function ($q) use ($request) {
            return $q->where('name_ar', 'like', '%' . $request->search . '%')->orWhere('name_en', 'like', '%' . $request->search . '%');
        })->latest()->paginate(10);

        return $this->sendResponse(['shifts' => $shifts], 'Data exited successfully');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(ShiftRequest $request)
    {
        Shift::create($request->validated());
        return $this->sendResponse([], 'Data exited successfully');

    }

    public function edit(Shift $shift)
    {
        return $this->sendResponse(['shift' => $shift], 'Data exited successfully');
    }




    public function update(ShiftRequest $request, Shift $shift)
    {
        $shift->update($request->validated());
        return $this->sendResponse([], 'Data exited successfully');
    }


    public function destroy(Shift $shift)
    {
        if (count($shift->shifts) == 0)
        {
            $shift->delete();
            return $this->sendResponse([],'Deleted successfully');
        }
        return $this->sendError('Cant delete');

    }
}
