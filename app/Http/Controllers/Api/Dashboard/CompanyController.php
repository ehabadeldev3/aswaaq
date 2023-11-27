<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\CompanyRequest;
use App\Models\Company;
use App\Traits\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    use Message;

    public function __construct()
    {
        $this->middleware('permission:company read', ['only' => ['index']]);
        $this->middleware('permission:company create', ['only' => ['store']]);
        $this->middleware('permission:company edit', ['only' => ['update', 'edit','activationCompany']]);
        $this->middleware('permission:company delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $categories = Company::with('media:file_name,mediable_id')
            ->when($request->search, function ($q) use ($request) {
                return $q->where('name_ar', 'like', '%' . $request->search . '%')->orWhere('name_en', 'like', '%' . $request->search . '%');
            })->latest()->paginate(10);

        return $this->sendResponse(['categories' => $categories], 'Data exited successfully');
    }


    public function activationCompany(Company $company)
    {
        $company->update([
            "status" => $company->status == 1 ? 0 : 1
        ]);
        return $this->sendResponse([], 'Data exited successfully');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CompanyRequest $request)
    {

        $data = $request->only(['name_ar','name_en']);

        $company = Company::create($data);

        if ($request->hasFile('file'))
            saveFile($request->file,$company,'company');

        return $this->sendResponse([], 'Data exited successfully');

    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $company = Company::with('media:file_name,mediable_id')->find($id);
        return $this->sendResponse(['company' => $company], 'Data exited successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CompanyRequest $request, Company $company)
    {
        $data = $request->only(['name_ar','name_en','status']);

        $company->update($data);

        if ($request->hasFile('file'))
            saveFile($request->file,$company,'company','update');


        return $this->sendResponse([],'Data exited successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
        if (count($company->products) == 0){
           deleteFile($company);
            $company->delete();
            return $this->sendResponse([],'Deleted successfully');
        }
        return $this->sendError('Cant delete');
    }
}
