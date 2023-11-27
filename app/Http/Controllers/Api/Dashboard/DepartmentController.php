<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\DepartmentRequest;
use App\Models\Department;
use App\Traits\Message;
use Illuminate\Http\Request;
class DepartmentController extends Controller
{
    use Message;


    public function __construct()
    {
        $this->middleware('permission:department read', ['only' => ['index','activeDepartment']]);
        $this->middleware('permission:department create', ['only' => ['store','create']]);
        $this->middleware('permission:department edit', ['only' => ['update', 'edit','activationDepartment']]);
        $this->middleware('permission:department delete', ['only' => ['destroy']]);
    }

    /**
     * get active Department
     */
    public function activeDepartment()
    {
        $departments = Department::where('active', 1)->get();
        return $this->sendResponse(['departments' => $departments], 'Data exited successfully');
    }


    /**
     * activation Department
     */
    public function activationDepartment(Department $department)
    {

        $department->update([
            "active" => $department->active == 1 ? 0 : 1
        ]);

        return $this->sendResponse([], 'Data exited successfully');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $departments = Department::when($request->search, function ($q) use ($request) {
            return $q
            ->where('name_ar', 'like', '%' . $request->search . '%')
            ->where('name_en', 'like', '%' . $request->search . '%');
        })->latest()->paginate(10);

        return $this->sendResponse(['departments' => $departments], 'Data exited successfully');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(DepartmentRequest $request)
    {
        Department::create($request->validated());
        return $this->sendResponse([], 'Data exited successfully');
    }

    public function edit(Department $department)
    {
        return $this->sendResponse(['department' => $department], 'Data exited successfully');
    }



    public function update(DepartmentRequest $request, Department $department)
    {
        $department->update($request->validated());
        return $this->sendResponse([], 'Data exited successfully');

    }


    public function destroy(Department $department)
    {
        if (count($department->employees) == 0)
        {
            $department->delete();
            return $this->sendResponse([],'Deleted successfully');
        }
        return $this->sendError('Cant delete');

    }
}
