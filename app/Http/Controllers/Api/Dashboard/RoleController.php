<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\RoleRequest;
use App\Models\Notify;
use App\Traits\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{

    use Message;

    public function __construct()
    {
        $this->middleware('permission:role read', ['only' => ['index']]);
        $this->middleware('permission:role create', ['only' => ['store','create']]);
        $this->middleware('permission:role edit', ['only' => ['update', 'edit']]);
        $this->middleware('permission:role delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $roles = Role::select('name', 'id')->whereNotIn('name',['SuperAdmin','Dispatcher','LoadingMan'])->with('permissions:name')
        ->where(function ($q) use ($request) {
            $q->when(true, function ($q) use ($request) {
                $q->where('name', 'like', "%$request->search%")
                    ->orWhereRelation('permissions', 'name', "%$request->search%");
            });
        })->latest()->paginate(10);
        return $this->sendResponse(['roles' => $roles], 'Data exited successfully');

    }


    public function create()
    {
        $permission = Permission::get()->groupBy('category')->sortKeys();
        $notifies = Notify::select('id','name')->get();
        return $this->sendResponse(['permission' => $permission, 'notifies' => $notifies], 'Data exited successfully');
    }


    public function store(RoleRequest $request)
    {
        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($request->input('permission'));
        $role->notify()->attach($request->notify);
        return $this->sendResponse([], 'Data exited successfully');
    }


    public function edit(Role $role)
    {
        $permission = Permission::get()->groupBy('category')->sortKeys();

        $rolePermissions = DB::table("role_has_permissions")
            ->where("role_has_permissions.role_id", $role->id)->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')->all();
        $notifies = Notify::select('id','name')->get();

        $notifyRole = Notify::whereRelation('role', 'role_id', $role->id)->pluck('id', 'name')->all();

        return $this->sendResponse(['notifyRole' => $notifyRole, 'role' => $role,'notifies' => $notifies, 'permission' => $permission, 'rolePermissions' => $rolePermissions], 'Data exited successfully');
    }

    public function update(RoleRequest $request, Role $role)
    {
        $role->name = $request->input('name');
        $role->save();
        $role->syncPermissions($request->input('permission'));
        $role->notify()->sync($request->notify);

        return $this->sendResponse([], 'Edited successfully');
    }

    public function destroy(Role $role)
    {
        if (count($role->users) == 0) {
            DB::table("roles")->where('id', $role->id)->delete();
            return $this->sendResponse([], 'Deleted successfully');
        }
        return $this->sendError('Cant delete');
    }
}

