<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\DispatcherRequest;
use App\Models\Area;
use App\Models\Dispatcher;
use App\Models\User;
use App\Traits\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DispatcherController extends Controller
{
    use Message;

    public function __construct()
    {
        $this->middleware('permission:dispatcher read', ['only' => ['index']]);
        $this->middleware('permission:dispatcher create', ['only' => ['store','create']]);
        $this->middleware('permission:dispatcher edit', ['only' => ['update', 'edit','activationLoadingMan']]);
        $this->middleware('permission:dispatcher delete', ['only' => ['destroy']]);
        $this->middleware('permission:dispatcher ChangePassword edit', ['only' => ['changePassword']]);
    }

    /**
     * change Password
     */

    public function changePassword(Request $request, Dispatcher $dispatcher)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $dispatcher->user->update(["password" => $request->password]);
        return $this->sendResponse([], 'Data exited successfully');
    }


    /**
     * activation Dispatcher
     */
    public function activationDispatcher(Dispatcher $dispatcher)
    {
        $dispatcher->user->update([
            "status" => $dispatcher->user->status == 1 ? 0 : 1
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
        $dispatchers = Dispatcher::with('user:id,name,email,phone,status','suppliers')
            ->where(function ($q) use($request){
                $q->when($request->search,function ($q) use($request){
                    return $q->OrWhere('hiring_date','like','%'.$request->search.'%')
                    ->orWhereRelation('user',function($q) use($request){
                        $q->where('email','like','%'.$request->search.'%')
                        ->OrWhere('name','like','%'.$request->search.'%')
                        ->OrWhere('phone','like','%'.$request->search.'%');
                    });
                });
            })->latest()->paginate(10);

        return $this->sendResponse(['dispatchers' => $dispatchers], 'Data exited successfully');
    }



    public function store(DispatcherRequest $request)
    {
        // start create user
        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => $request->password,
            "auth_id" => 1,
            'role_name' => ['dispatcher'],
            "status" => 1,
            'phone' => $request->phone
        ]);

        $user->dispatcher()->create([
            'address' => $request->address,
            'National_ID' => $request->National_ID,
            'birth_date' => $request->birth_date,
            'hiring_date' => $request->hiring_date,
            'salary' => $request->salary,
        ]);

        $role = Role::where('name' ,'Dispatcher')->first() ?? Role::create(['name' => 'Dispatcher']);
        $user->assignRole([$role->id]);
        $request_permissions = explode(',',$request->permissions);
        $this->assignPermissionsToTheRole($role,$request_permissions);

        $suppliers = explode(',',$request->supplier_id);
        $user->dispatcher->suppliers()->attach($suppliers);

        saveFile($request->file,$user,'dispatcher_profile');

        return $this->sendResponse([], 'Data exited successfully');
    }

    public function edit($id)
    {

        $dispatcher = Dispatcher::with('user:id,name,email,phone,status,role_name','suppliers')->find($id)->makeVisible('translations');
        $role = Role::where('name' ,'Dispatcher')->first();

        $permissions = $role->permissions()->whereNotIn('name',['orderOnline read','orderOnline edit','supplier read','supplier edit','supplier create','supplier delete','virtualStock read','virtualStock create','virtualStock edit','virtualStock delete'])->get()->pluck('id')->toArray();

        $media = $dispatcher->user->media->file_name;
        return $this->sendResponse(['dispatcher' => $dispatcher, 'media' => $media,'permissions' => $permissions], 'Data exited successfully');
    }


    public function update(DispatcherRequest $request, Dispatcher $dispatcher)
    {
        $dispatcher->user()->update([
            "name" => $request->name,
            "email" => $request->email,
            'phone' => $request->phone
        ]);

        $dispatcher->update([
            'address' => $request->address,
            'National_ID' => $request->National_ID,
            'birth_date' => $request->birth_date,
            'hiring_date' => $request->hiring_date,
            'salary' => $request->salary,
        ]);

        $role = Role::where('name' ,'Dispatcher')->first();
        $request_permissions = explode(',',$request->permissions);
        $this->assignPermissionsToTheRole($role,$request_permissions);

        $suppliers = explode(',',$request->supplier_id);
        $dispatcher->suppliers()->sync($suppliers);
        if ($request->hasFile('file'))
            saveFile($request->file,$dispatcher->user,'dispatcher_profile','update');

        return $this->sendResponse([], 'Data exited successfully');

    }


    protected function assignPermissionsToTheRole($role,$request_permissions)
    {
        $fixed_dispatcher_permissions = Permission::whereIn('name',['orderOnline read','orderOnline edit','supplier read','supplier edit','supplier create','supplier delete','virtualStock read','virtualStock create','virtualStock edit','virtualStock delete'])->get()->pluck('id')->toArray();
        $all_permissions = array_merge($fixed_dispatcher_permissions,$request_permissions);
        $role->syncPermissions($all_permissions);
    }



    public function get_all_additional_dispatcher_permissions()
    {
        $permissions = Permission::whereNotIn('name',['orderOnline read','orderOnline edit','supplier read','supplier edit','supplier create','supplier delete','virtualStock read','virtualStock create','virtualStock edit','virtualStock delete'])->get();
        return $this->sendResponse(['permissions' => $permissions], 'Data exited successfully');

    }


    public function destroy($id)
    {
       //
    }
}
