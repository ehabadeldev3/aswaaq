<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\TaxRequest;
use App\Models\Tax;
use App\Traits\Message;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    use Message;

    public function __construct()
    {
        $this->middleware('permission:tax read', ['only' => ['index']]);
        $this->middleware('permission:tax create', ['only' => ['store']]);
        $this->middleware('permission:tax edit', ['only' => ['update', 'edit','activationTax']]);
        $this->middleware('permission:tax delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $taxs = Tax::
        when($request->search, function ($q) use ($request) {
            return $q->where('name_ar', 'like', '%' . $request->search . '%')->orWhere('name_en', 'like', '%' . $request->search . '%');
        })
        ->latest()->paginate(10);

        return $this->sendResponse(['taxs' => $taxs], 'Data exited successfully');
    }

    //change status

    public function activationTax(Tax $tax)
    {
        $tax->update([
            "status" => $tax->status == 1 ? 0: 1
        ]);
        return $this->sendResponse([], 'Data exited successfully');
    }



    public function store(TaxRequest $request)
    {
        Tax::create($request->validated());
        return $this->sendResponse([], 'Data exited successfully');
    }




    public function edit(Tax $tax)
    {
        return $this->sendResponse(['tax' => $tax], 'Data exited successfully');
    }


    public function update(TaxRequest $request, Tax $tax)
    {
        $tax->update($request->validated());
        return $this->sendResponse([],'Data exited successfully');
    }


    public function destroy(Tax $tax)
    {
        $tax->delete();
        return $this->sendResponse([], 'Deleted successfully');
    }
}
