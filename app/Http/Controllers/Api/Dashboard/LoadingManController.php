<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\LoadingManRequest;
use App\Models\LoadingMan;
use App\Models\User;
use App\Traits\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class LoadingManController extends Controller
{
    use Message;

    public function __construct()
    {
        $this->middleware('permission:loading man read', ['only' => ['index']]);
        $this->middleware('permission:loading man create', ['only' => ['store','create']]);
        $this->middleware('permission:loading man edit', ['only' => ['update', 'edit','activationLoadingMan']]);
        $this->middleware('permission:loading man delete', ['only' => ['destroy']]);
        $this->middleware('permission:loading man ChangePassword edit', ['only' => ['changePassword']]);
    }

    public function changePassword(Request $request, LoadingMan $loadingMan)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $loadingMan->user->update(["password" => $request->password]);
    }

    /**
     * activation loadingMan
     */
    public function activationLoadingMan(LoadingMan $loadingMan)
    {
        $loadingMan->user->update([
            "status" => $loadingMan->user->status == 1 ? 0:1
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
        $loading_men = LoadingMan::with('user:id,name,email,phone,status','areas')
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

        return $this->sendResponse(['loading_men' => $loading_men], 'Data exited successfully');
    }



    public function store(LoadingManRequest $request)
    {
        // start create user
        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => $request->password,
            "auth_id" => 1,
            'role_name' => ['loading_man'],
            "status" => 1,
            'phone' => $request->phone
        ]);

        $user->loading_man()->create([
            'address' => $request->address,
            'National_ID' => $request->National_ID,
            'birth_date' => $request->birth_date,
            'hiring_date' => $request->hiring_date,
            'salary' => $request->salary,
        ]);

        $role = Role::where('name' ,'LoadingMan')->first() ?? Role::create(['name' => 'LoadingMan']);
        $user->assignRole([$role->id]);
        $request_permissions = explode(',',$request->permissions);
        $this->assignPermissionsToTheRole($role,$request_permissions);

        $areas = explode(',',$request->area_id);
        $user->loading_man->areas()->attach($areas);

        saveFile($request->file,$user,'loading_man_profile');

        return $this->sendResponse([], 'Data exited successfully');

    }

    public function edit($id)
    {
        $loading_man = LoadingMan::with('user:id,name,email,phone,status,role_name','areas')->find($id)->makeVisible('translations');
        $role = Role::where('name' ,'LoadingMan')->first();
        $permissions = $role->permissions()->whereNotIn('name',['orderOnline read','run sheet edit','run sheet read'])->get()->pluck('id')->toArray();
        $media = $loading_man->user->media->file_name;
        return $this->sendResponse(['loading_man' => $loading_man, 'media' => $media, 'permissions' =>$permissions], 'Data exited successfully');
    }



    public function update(LoadingManRequest $request, LoadingMan $loadingMan)
    {
        $loadingMan->user()->update([
            "name" => $request->name,
            "email" => $request->email,
            'phone' => $request->phone
        ]);

        $loadingMan->update([
            'address' => $request->address,
            'National_ID' => $request->National_ID,
            'birth_date' => $request->birth_date,
            'hiring_date' => $request->hiring_date,
            'salary' => $request->salary,
        ]);

        $role = Role::where('name' ,'Dispatcher')->first();
        $request_permissions = explode(',',$request->permissions);
        $this->assignPermissionsToTheRole($role,$request_permissions);


        $areas = explode(',',$request->area_id);
        $loadingMan->areas()->sync($areas);

        if ($request->hasFile('file'))
            saveFile($request->file,$loadingMan->user,'loading_man_profile','update');



        return $this->sendResponse([], 'Data exited successfully');

    }

    protected function assignPermissionsToTheRole($role,$request_permissions)
    {
        $fixed_dispatcher_permissions = Permission::whereIn('name',['orderOnline read','run sheet edit','run sheet read'])->get()->pluck('id')->toArray();
        $all_permissions = array_merge($fixed_dispatcher_permissions,$request_permissions);
        $role->syncPermissions($all_permissions);
    }



    public function get_all_additional_loading_man_permissions()
    {
        $permissions = Permission::whereNotIn('name',['orderOnline read','run sheet edit','run sheet read'])->get();
        return $this->sendResponse(['permissions' => $permissions], 'Data exited successfully');

    }

}
