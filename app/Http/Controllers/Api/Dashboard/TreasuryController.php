<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\TreasuryRequest;
use App\Models\IncomeAndExpense;
use App\Models\Treasury;
use App\Traits\Message;
use Illuminate\Http\Request;


class TreasuryController extends Controller
{
    use Message;

    public function __construct()
    {
        $this->middleware('permission:treasury read', ['only' => ['index','activeTreasury','mainTreasury']]);
        $this->middleware('permission:treasury create', ['only' => ['store']]);
        $this->middleware('permission:treasury edit', ['only' => ['activationTreasury','edit','update']]);
        $this->middleware('permission:treasury delete', ['only' => ['destroy']]);
        $this->middleware('permission:treasuriesIncome read', ['only' => ['treasuriesIncome']]);
        $this->middleware('permission:treasuriesExpense read', ['only' => ['treasuriesExpense']]);
    }
    /**
     * get treasuries expense
     */
    public function treasuriesExpense(Request $request,$id)
    {
        $incomeAndExpense = IncomeAndExpense::with('expense', 'user')->whereNotNull('expense_id')->where('treasury_id',$id)
            ->where(function ($q) use ($request) {
                $q->when($request->search, function ($q) use ($request) {
                    return $q->OrWhere('notes', 'like', '%' . $request->search . '%')
                        ->orWhere('amount', 'like', '%' . $request->search . '%')
                        ->orWhere('payment_date', 'like', '%' . $request->search . '%')
                        ->orWhere('payer', 'like', '%' . $request->search . '%')
                        ->orWhereRelation('user', 'name', 'like', '%' . $request->search . '%')
                        ->orWhereRelation('expense.translations', 'name', 'like', '%' . $request->search . '%');
                });

            })->latest()->paginate(15);

        return $this->sendResponse(['incomes' => $incomeAndExpense], 'Data exited successfully');
    }



    /**
     * get treasuries income
     */
    public function treasuriesIncome(Request $request,$id)
    {
        $incomeAndExpense = IncomeAndExpense::with('income', 'user')->whereNotNull('income_id')->where('treasury_id',$id)
            ->where(function ($q) use ($request) {
                $q->when($request->search, function ($q) use ($request) {
                    return $q->where('notes', 'like', '%' . $request->search . '%')
                        ->orWhere('amount', 'like', '%' . $request->search . '%')
                        ->orWhere('payment_date', 'like', '%' . $request->search . '%')
                        ->orWhere('payer', 'like', '%' . $request->search . '%')
                        ->orWhereRelation('user', 'name', 'like', '%' . $request->search . '%')
                        ->orWhere('income', 'name', 'like', '%' . $request->search . '%');
                });

            })->latest()->paginate(15);


        return $this->sendResponse(['incomes' => $incomeAndExpense], 'Data exited successfully');
    }




    /**
     * get active treasury
     */
    public function activeTreasury()
    {
        $treasuries = Treasury::where('active', 1)->get();
        return $this->sendResponse(['treasuries' => $treasuries], 'Data exited successfully');
    }

    /**
     * activation Income
     */
    public function activationTreasury(Treasury $treasury)
    {
        $treasury->update([
            "active" =>$treasury->active == 1? 0:1
        ]);
        return $this->sendResponse([], 'Data exited successfully');
    }

    /**
     * get main treasury
     */
    public function mainTreasury()
    {
        $treasuries = Treasury::where([
            ['treasury_id', null],
            ['active', 1],
        ])->get();
        return $this->sendResponse(['treasuries' => $treasuries], 'Data exited successfully');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $treasuries = Treasury::with('treasuryParent')->when($request->search, function ($q) use ($request) {
            return $q->where('name_en', 'like', '%' . $request->search . '%')->orWhere('name_ar', 'like', '%' . $request->search . '%');
        })->paginate(15);

        return $this->sendResponse(['treasuries' => $treasuries], 'Data exited successfully');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(TreasuryRequest $request)
    {
        $data = $request->validated();
        $treasury = Treasury::create($data);
        $treasury->treasuryBalance()->create([
            'amount' => $data['amount']
        ]);

         return $this->sendResponse([], 'Data exited successfully');
    }

    public function edit($id)
    {
        $treasury = Treasury::with('treasuryBalance')->find($id)->makeVisible('translations');
        $mainTreasury = Treasury::where([
            ['treasury_id', null],
            ['active', 1],
        ])->get();
        return $this->sendResponse(['treasury' => $treasury, 'mainTreasury' => $mainTreasury], 'Data exited successfully');
    }



    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(TreasuryRequest $request, Treasury $treasury)
    {
        $data = $request->validated();
        $treasury->update($data);
        $treasury->treasuryBalance()->updateOrCreate([
            'treasury_id'=>$treasury->id,
        ],[
            'amount' => $data['amount']
        ]);

        return $this->sendResponse([], 'Data exited successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Treasury $treasury)
    {
        if (count($treasury->incomeAndExpense) > 0)
        {
            return $this->sendError('can not delete');
        }

        $treasury->delete();
        return $this->sendResponse([],'Deleted successfully');
    }
}
