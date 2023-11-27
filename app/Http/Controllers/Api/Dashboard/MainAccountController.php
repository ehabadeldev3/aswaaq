<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\MainAccount;
use App\Models\SubAccount;
use App\Traits\AccountTrait;
use App\Traits\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MainAccountController extends Controller
{
    use Message;
    use AccountTrait;
    public function __construct()
    {
        $this->middleware('permission:AccountsTree read', ['only' => ['index','show']]);
        $this->middleware('permission:AccountsTree create', ['only' => ['store']]);
        $this->middleware('permission:AccountsTree edit', ['only' => ['update', 'edit']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $mains = MainAccount::get();
        return $this->sendResponse(['mains' => $mains], 'Data exited successfully');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', Rule::unique('sub_accounts', 'name')],
            'main_account_id' => 'required|integer|exists:main_accounts,id',
            'debit' => 'required'
        ]);

        SubAccount::create($data);
        return $this->sendResponse([], 'Data exited successfully');
    }

    public function edit(SubAccount $sub_account)
    {
        return $this->sendResponse(['income' => $sub_account], 'Data exited successfully');
    }


    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        $subAccounts = SubAccount::where('main_account_id',$id)->whereNull('sub_account_id')
            ->withCount('children')
            ->with('mainAccount')->when($request->search, function ($q) use ($request) {
                return $q->where('name', 'like', '%' . $request->search . '%');
            })->latest()->paginate(15);

        $items = collect($subAccounts->items())->map(function($item,int $key){
            $item['debit_amount'] = $this->debitAmount($item);
            $item['credit_amount'] = $this->creditAmount($item);
            return $item;
        });
        $subAccounts->setCollection($items);

        return $this->sendResponse(['subAccounts' => $subAccounts], 'Data exited successfully');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SubAccount $subAccount)
    {
        $data = $request->validate([
            'name' => ['required', Rule::unique('sub_accounts', 'name')->whereNot('id', $subAccount->id)],
            'debit' => 'required'
        ]);
        $subAccount->update($data);
        return $this->sendResponse([], 'Data exited successfully');
    }

}
