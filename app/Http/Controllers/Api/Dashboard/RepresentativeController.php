<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\RepresentativesRequest;
use App\Models\Area;
use App\Models\Representative;
use App\Models\User;
use App\Traits\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class RepresentativeController extends Controller
{
    use Message;

    public function __construct()
    {
        $this->middleware('permission:representative read', ['only' => ['index','activeRepresentative','get_representatives']]);
        $this->middleware('permission:representative create', ['only' => ['store','create']]);
        $this->middleware('permission:representative edit', ['only' => ['update', 'edit','activationRepresentative']]);
        $this->middleware('permission:representative delete', ['only' => ['destroy']]);
        $this->middleware('permission:representativeChangePassword edit', ['only' => ['changePassword']]);
    }

    /**
     * change Password
     */

    public function changePassword(Request $request,Representative $representative)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $representative->user->update(["password" => $request->password]);
    }

    /**
     * get active Representative
     */
    public function activeRepresentative()
    {
        $representatives = User::where('status',1)->whereAuthId(1)->whereJsonContains('role_name','representative')->get();
        return $this->sendResponse(['representatives' => $representatives], 'Data exited successfully');
    }

    /**
     * activation Representative
     */
    public function activationRepresentative(Representative $representative)
    {
        $representative->user->update([
            "status" => $representative->user->status == 1 ? 0:1
        ]);

        return $this->sendResponse([], 'Data exited successfully');
    }

    public function get_representatives(Request $request)
    {
        $representatives = User::whereHas('representative',function($q) use($request){
            $q->whereRelation('areas','areas.id',$request->area_id);
        })->where(function($q) use($request){
            $q->when($request->search,function($q) use($request){
                $q->where('name' , 'like' , "%$request->search%")
                ->orWhere('email' , 'like' , "%$request->search%")
                ->orWhere('phone' , 'like' , "%$request->search%");
            });
        })
        ->latest();
        if($request->paginate)
            $representatives = $representatives->take(10)->get();
        else
            $representatives = $representatives->get();

        return response()->json(['representatives' => $representatives],200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $representatives = Representative::with('user:id,name,email,phone,status','areas')
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

        return $this->sendResponse(['representatives' => $representatives], 'Data exited successfully');
    }



    public function store(RepresentativesRequest $request)
    {
        // start create user
        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => $request->password,
            "auth_id" => 1,
            'role_name' => ['representative'],
            "status" => 1,
            'phone' => $request->phone
        ]);

        $user->representative()->create([
            'address' => $request->address,
            'National_ID' => $request->National_ID,
            'birth_date' => $request->birth_date,
            'hiring_date' => $request->hiring_date,
            'salary' => $request->salary,
        ]);

        $areas = explode(',',$request->area_id);
        $user->representative->areas()->attach($areas);

        saveFile($request->file,$user,'representative_profile');

        return $this->sendResponse([], 'Data exited successfully');
    }

    public function edit($id)
    {
        $representative = Representative::with('user:id,name,email,phone,status,role_name','areas')->find($id)->makeVisible('translations');
        $media = $representative->user->media->file_name;

        return $this->sendResponse(['representative' => $representative, 'media' => $media], 'Data exited successfully');
    }



    public function update(RepresentativesRequest $request, Representative $representative)
    {
        $representative->user()->update([
            "name" => $request->name,
            "email" => $request->email,
            'phone' => $request->phone
        ]);

        $representative->update([
            'address' => $request->address,
            'National_ID' => $request->National_ID,
            'birth_date' => $request->birth_date,
            'hiring_date' => $request->hiring_date,
            'salary' => $request->salary,
        ]);
        $areas = explode(',',$request->area_id);
        $representative->areas()->sync($areas);

        if ($request->hasFile('file'))
            saveFile($request->file,$representative->user,'representative_profile','update');

        return $this->sendResponse([], 'Data exited successfully');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       //
    }
}
