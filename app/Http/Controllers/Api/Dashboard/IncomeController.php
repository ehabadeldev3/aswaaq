<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\IncomeRequest;
use App\Models\Income;
use App\Traits\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class IncomeController extends Controller
{
    use Message;

    public function __construct()
    {
        $this->middleware('permission:income read', ['only' => ['index','activeIncome','mainIncome']]);
        $this->middleware('permission:income create', ['only' => ['store']]);
        $this->middleware('permission:income edit', ['only' => ['activationIncome','edit','update']]);
        $this->middleware('permission:income delete', ['only' => ['destroy']]);
    }
    /**
     * get active Income
     */
    public function activeIncome()
    {
        $incomes = Income::where('active', 1)->get();
        return $this->sendResponse(['incomes' => $incomes], 'Data exited successfully');
    }


    /**
     * get main Income
     */
    public function mainIncome()
    {
        $incomes = Income::where([
            ['income_id', null],
            ['active', 1],
        ])->get();
        return $this->sendResponse(['incomes' => $incomes], 'Data exited successfully');
    }

    /**
     * activation Income
     */
    public function activationIncome(Income $income)
    {
        $income->update([
            "active" =>$income->active == 1 ?  0: 1
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
        $incomes = Income::with('parent')->when($request->search, function ($q) use ($request) {
            return $q->where('name', 'like', '%' . $request->search . '%');
        })->latest()->paginate(15);

        return $this->sendResponse(['incomes' => $incomes], 'Data exited successfully');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(IncomeRequest $request)
    {
        Income::create($request->validated());
        return $this->sendResponse([], 'Data exited successfully');
    }

    public function edit(Income $income)
    {

        $mainIncome = Income::where([
            ['income_id', null],
            ['active', 1],
        ])->get();

        return $this->sendResponse(['income' => $income, 'mainIncome' => $mainIncome], 'Data exited successfully');
    }



    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(IncomeRequest $request, Income $income)
    {
        $income->update($request->validated());
        return $this->sendResponse([], 'Data exited successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Income $income)
    {
        if (count($income->incomeAndExpense) > 0 || $income->id <= 3)
        {
            return $this->sendError('can not delete');
        }

        $income->delete();

        return $this->sendResponse([], 'Data exited successfully');
    }
}
