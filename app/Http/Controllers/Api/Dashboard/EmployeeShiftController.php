<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\EmployeeShiftsRequest;
use App\Models\Employee;
use App\Models\EmployeeShift;
use App\Models\Shift;
use App\Traits\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EmployeeShiftController extends Controller
{
    use Message;

    public function __construct()
    {
        $this->middleware('permission:employeeShifts read', ['only' => ['index']]);
        $this->middleware('permission:employeeShifts create', ['only' => ['store','create']]);
        $this->middleware('permission:employeeShifts edit', ['only' => ['update','edit']]);
        $this->middleware('permission:employeeShifts delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $shifts = EmployeeShift::with(['employee.user:name,id','name','Shift'])
            ->where(function ($q) use ($request) {
                $q->when($request->search, function ($q) use ($request) {
                    return $q->whereRelation('employee.user','name','like','%'.$request->search.'%')
                        ->orWhereRelation('Shift','name','like','%'.$request->search.'%');
                });
            })->latest()->paginate(10);

        return $this->sendResponse(['shifts' => $shifts], 'Data exited successfully');
    }

    public function create(){
        $shifts = Shift::where('active', 1)->get();
        $employees = Employee::with('user')->whereRelation('user','status',1)->get();
        return $this->sendResponse(['shifts' => $shifts,'employees'=>$employees], 'Data exited successfully');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(EmployeeShiftsRequest $request)
    {
        EmployeeShift::create($request->validated());
        return $this->sendResponse([], 'Data exited successfully');
    }

    public function edit(EmployeeShift $employeeShift)
    {
        $shifts = Shift::where('active', 1)->get();
        $employees = Employee::with('user')->whereRelation('user','status',1)->get();
        return $this->sendResponse(['shifts' => $shifts,'employees'=>$employees,'shift'=>$employeeShift], 'Data exited successfully');
    }



    public function update(EmployeeShiftsRequest $request,EmployeeShift $employeeShift)
    {
        $employeeShift->update($request->validated());
        return $this->sendResponse([], 'Data exited successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmployeeShift $employeeShift)
    {
        $employeeShift->delete();
        return $this->sendResponse([],'Deleted successfully');
    }
}
