<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\DiscountRequest;
use App\Models\Discount;
use App\Traits\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DiscountController extends Controller
{

    use Message;

    public function __construct()
    {
        $this->middleware('permission:discount read', ['only' => ['index']]);
        $this->middleware('permission:discount create', ['only' => ['store']]);
        $this->middleware('permission:discount edit', ['only' => ['update', 'edit','activationDiscount']]);
        $this->middleware('permission:discount delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $coupons = Discount::when($request->search,function ($q) use ($request){

            return $q->where('code','like',"%". $request->search ."%");

        })->latest('id','ASC')->paginate(10);

        return $this->sendResponse(['coupons' => $coupons], 'Data exited successfully');
    }

    public function activationDiscount(Discount $discount)
    {
        $discount->update([
            "status" => $discount->status == 1 ? 0 : 1
        ]);
        return $this->sendResponse([], 'Data exited successfully');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DiscountRequest $request)
    {
        Discount::create($request->validated());
        return $this->sendResponse([], 'Data exited successfully');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Discount $discount)
    {
        return $this->sendResponse(['discount' => $discount], 'Data exited successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DiscountRequest $request, Discount $discount)
    {
        $discount->update($request->validated());
        return $this->sendResponse([],'Data exited successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Discount $discount)
    {
        $discount->delete();
        return $this->sendResponse([],'Deleted successfully');
    }
}
