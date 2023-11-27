<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\TransferringTreasuryRequest;
use App\Models\TransferringTreasury;
use App\Models\Treasury;
use App\Traits\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransferringTreasuryController extends Controller
{
    use Message;
    public function __construct()
    {
        $this->middleware('permission:transferringTreasury read', ['only' => ['index']]);
        $this->middleware('permission:transferringTreasury create', ['only' => ['store']]);
        $this->middleware('permission:transferringTreasury edit', ['only' => ['edit','update']]);
        $this->middleware('permission:transferringTreasury delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $treasuries = TransferringTreasury::with('user','fromTreasury','toTreasury')->when($request->search, function ($q) use ($request) {
            return $q->whereRelation('fromTreasury', 'name', 'like', '%' . $request->search . '%')
                ->orWhereRelation('toTreasury', 'name', 'like', '%' . $request->search . '%')
                ->orWhereRelation('user', 'name', 'like', '%' . $request->search . '%');
        })->paginate(15);

        return $this->sendResponse(['treasuries' => $treasuries], 'Data exited successfully');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(TransferringTreasuryRequest $request)
    {
        $data = $request->validated();
        $data['user_id']=auth()->user()->id;
        TransferringTreasury::create($data);
        return $this->sendResponse([], 'Data exited successfully');
    }

    public function edit(TransferringTreasury $transferringTreasury)
    {
        $treasuries = Treasury::where('active', 1)->get();
        return $this->sendResponse(['treasury' => $transferringTreasury,'treasuries'=>$treasuries], 'Data exited successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(TransferringTreasuryRequest $request, TransferringTreasury $transferringTreasury)
    {
        $data = $request->validated();
        $data['user_id']=auth()->user()->id;
        $transferringTreasury->update($data);

        return $this->sendResponse([], 'Data exited successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(TransferringTreasury $transferringTreasury)
    {
        $transferringTreasury->delete();

        return $this->sendResponse([], 'Data exited successfully');
    }
}
