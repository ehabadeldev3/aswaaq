<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\IncomeAndExpenseRequest;
use App\Models\Income;
use App\Models\IncomeAndExpense;
use App\Models\Treasury;
use App\Traits\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class IncomeAndExpenseController extends Controller
{
    use Message;

    public function __construct()
    {
        $this->middleware('permission:income&expense read', ['only' => ['calcIncome','calcExpense']]);
        $this->middleware('permission:income&expense create', ['only' => ['store']]);
        $this->middleware('permission:income&expense edit', ['only' => ['editExpense','edit','update']]);
        $this->middleware('permission:income&expense delete', ['only' => ['destroy']]);
    }

    // get calculate income
    public function calcIncome(Request $request)
    {
        $incomeAndExpense = IncomeAndExpense::with('income', 'user')->whereNotNull('income_id')
            ->where(function ($q) use ($request) {
                $q->when($request->search, function ($q) use ($request) {
                    return $q->where('notes', 'like', '%' . $request->search . '%')
                        ->orWhere('amount', 'like', '%' . $request->search . '%')
                        ->orWhere('payment_date', 'like', '%' . $request->search . '%')
                        ->orWhere('payer', 'like', '%' . $request->search . '%')
                        ->orWhereRelation('user', 'name', 'like', '%' . $request->search . '%')
                        ->orWhereRelation('income', 'name', 'like', '%' . $request->search . '%');
                });

            })->latest()->paginate(15);

        return $this->sendResponse(['calcIncome' => $incomeAndExpense], 'Data exited successfully');
    }

    // get calculate expense

    public function calcExpense(Request $request)
    {
        $incomeAndExpense = IncomeAndExpense::with('expense', 'user')->whereNotNull('expense_id')
            ->where(function ($q) use ($request) {
                $q->when($request->search, function ($q) use ($request) {
                    return $q->where('notes', 'like', '%' . $request->search . '%')
                        ->orWhere('amount', 'like', '%' . $request->search . '%')
                        ->orWhere('payment_date', 'like', '%' . $request->search . '%')
                        ->orWhere('payer', 'like', '%' . $request->search . '%')
                        ->orWhereRelation('user', 'name', 'like', '%' . $request->search . '%')
                        ->orWhereRelation('expense', 'name', 'like', '%' . $request->search . '%');
                });

            })->latest()->paginate(15);

        return $this->sendResponse(['calcExpense' => $incomeAndExpense], 'Data exited successfully');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(IncomeAndExpenseRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->user()->id;
        IncomeAndExpense::create($data);
        return $this->sendResponse([], 'Data exited successfully');
    }

    public function edit(IncomeAndExpense $incomeAndExpense)
    {
        $mainIncome = Income::where('active', 1)->get();
        $treasuries = Treasury::where('active', 1)->get();
        return $this->sendResponse(['income' => $incomeAndExpense, 'mainIncome' => $mainIncome,'treasuries' => $treasuries], 'Data exited successfully');
    }

    public function editExpense(IncomeAndExpense $incomeAndExpense)
    {
        $mainExpense = Income::where('active', 1)->get();
        $treasuries = Treasury::where('active', 1)->get();
        return $this->sendResponse(['expense' => $incomeAndExpense, 'mainExpense' => $mainExpense,'treasuries' => $treasuries], 'Data exited successfully');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(IncomeAndExpenseRequest $request, IncomeAndExpense $incomeAndExpense)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->user()->id;
        $incomeAndExpense->update($data);
        return $this->sendResponse([], 'Data exited successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(IncomeAndExpense $incomeAndExpense)
    {
        $incomeAndExpense->delete();
        return $this->sendResponse([], 'Deleted successfully');

    }
}
