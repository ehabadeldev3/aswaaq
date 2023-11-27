<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\ExpenseRequest;
use App\Models\Expense;
use App\Traits\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ExpenseController extends Controller
{
    use Message;
    public function __construct()
    {
        $this->middleware('permission:expense read', ['only' => ['index','activeExpense','mainExpense']]);
        $this->middleware('permission:expense create', ['only' => ['store']]);
        $this->middleware('permission:expense edit', ['only' => ['activationExpense','edit','update']]);
        $this->middleware('permission:expense delete', ['only' => ['destroy']]);
    }
    /**
     * get active Income
     */
    public function activeExpense()
    {
        $expenses = Expense::where('active', 1)->get();
        return $this->sendResponse(['expenses' => $expenses], 'Data exited successfully');
    }

    /**
     * get main Expense
     */
    public function mainExpense()
    {
        $expenses = Expense::where([
            ['expense_id', null],
            ['active', 1],
        ])->get();
        return $this->sendResponse(['expenses' => $expenses], 'Data exited successfully');
    }

    /**
     * activation Expense
     */
    public function activationExpense(Expense $expense)
    {
        $expense->update([
            "active" =>$expense->active == 1 ?0 : 1
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
        $expenses = Expense::with('parent')->when($request->search, function ($q) use ($request) {
            return $q->where('name', 'like', '%' . $request->search . '%');
        })->latest()->paginate(15);

        return $this->sendResponse(['expenses' => $expenses], 'Data exited successfully');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(ExpenseRequest $request)
    {
        Expense::create($request->validated());
        return $this->sendResponse([], 'Data exited successfully');
    }

    public function edit(Expense $expense)
    {
        $mainExpense = Expense::where([
            ['expense_id', null],
            ['active', 1],
        ])->get();

        return $this->sendResponse(['expense' => $expense, 'mainExpense' => $mainExpense], 'Data exited successfully');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(ExpenseRequest $request, Expense $expense)
    {
        $expense->update($request->validated());
        return $this->sendResponse([], 'Data exited successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Expense $expense)
    {
        if (count($expense->incomeAndExpense) > 0)
        {
            return $this->sendError('can not delete');
        }

        $expense->delete();

        return $this->sendResponse([], 'Data exited successfully');
    }
}
