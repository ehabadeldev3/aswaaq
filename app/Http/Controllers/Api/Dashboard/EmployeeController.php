<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\EmployeeRequest;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Income;
use App\Models\Job;
use App\Models\Notify;
use App\Models\User;
use App\Traits\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    use Message;

    public function __construct()
    {
        $this->middleware('permission:employee read', ['only' => ['index','all_roles']]);
        $this->middleware('permission:employee create', ['only' => ['store']]);
        $this->middleware('permission:employee edit', ['only' => ['update', 'edit','activationEmployee']]);
        $this->middleware('permission:employee delete', ['only' => ['destroy']]);
        $this->middleware('permission:employeeChangePassword edit', ['only' => ['changePassword']]);
    }

    public function changePassword(Request $request,Employee $employee)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $employee->user->update(["password" => $request->password]);

        return $this->sendResponse([], 'Data exited successfully');
    }

    public function all_roles()
    {
        $roles = DB::table('roles')->whereNotIn('name',['SuperAdmin','Dispatcher','LoadingMan'])->get();
        return $this->sendResponse(['roles' => $roles], 'Data exited successfully');
    }

    /**
     * activation Income
     */
    public function activationEmployee(Employee $employee)
    {
        $employee->user->update([
            "status" => $employee->status == 1 ? 0 : 1
        ]);

        return $this->sendResponse([], 'Data exited successfully');
    }


    public function index(Request $request)
    {
        $employees = Employee::with('user:id,name,email,phone,status', 'job', 'department')
            ->where(function ($q) use($request){
            $q->when($request->search,function ($q) use($request){
                return $q->OrWhere('hiring_date','like','%'.$request->search.'%')
                    ->orWhereRelation('user','email','like','%'.$request->search.'%')
                    ->orWhereRelation('user','name','like','%'.$request->search.'%')
                    ->orWhereRelation('user','phone','like','%'.$request->search.'%')
                    ->orWhereRelation('department','name','like','%'.$request->search.'%')
                    ->orWhereRelation('job','name','like','%'.$request->search.'%');
            });

        })->latest()->paginate(15);

        return $this->sendResponse(['employees' => $employees], 'Data exited successfully');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(EmployeeRequest $request)
    {
        // start create user
        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => $request->password,
            "auth_id" => 1,
            'role_name' => [$request->role_name],
            "status" => 1,
            'phone' => $request->phone
        ]);
        $user->assignRole($request->input('role_name'));

        $user->employee()->create([
            'department_id' => $request->department_id,
            'job_id' => $request->job_id,
            'address' => $request->address,
            'National_ID' => $request->National_ID,
            'birth_date' => $request->birth_date,
            'hiring_date' => $request->hiring_date,
            'salary' => $request->salary,
        ]);
        $user->banks()->create([
            'name' => $request->bank_name,
            'address' => $request->bank_address,
            'iban' => $request->bank_iban,
            'swift' => $request->bank_swift
        ]);

        saveFile($request->file,$user,'user');

        return $this->sendResponse([], 'Data exited successfully');
    }

    public function edit($id)
    {

        $employee = Employee::with('user:id,name,email,phone,status,role_name')->find($id);
        $media = $employee->user->media->file_name;
        $employee['bank'] = $employee->user->banks;
        $department = Department::where('active', 1)->get();
        $job = Job::where('active', 1)->get();
        $roles = DB::table('roles')->whereNotIn('name',['SuperAdmin','Dispatcher','LoadingMan'])->get();

        return $this->sendResponse(['employee' => $employee, 'departments' => $department, 'jobs' => $job, 'roles' => $roles, 'media' => $media], 'Data exited successfully');

    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(EmployeeRequest $request, Employee $employee)
    {
        $employee->user()->update([
            "name" => $request->name,
            "email" => $request->email,
            'role_name' => [$request->role_name],
            'phone' => $request->phone
        ]);

        $employee->user->assignRole($request->input('role_name'));

        $employee->update([
            'department_id' => $request->department_id,
            'job_id' => $request->job_id,
            'address' => $request->address,
            'National_ID' => $request->National_ID,
            'birth_date' => $request->birth_date,
            'hiring_date' => $request->hiring_date,
            'salary' => $request->salary,
        ]);

        $employee->user->banks->first()->update([
            'name' => $request->bank_name,
            'address' => $request->bank_address,
            'iban' => $request->bank_iban,
            'swift' => $request->bank_swift
        ]);

        if ($request->hasFile('file'))
            saveFile($request->file,$employee->user,'user','update');

        return $this->sendResponse([], 'Data exited successfully');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Income $income)
    {

    }
}
