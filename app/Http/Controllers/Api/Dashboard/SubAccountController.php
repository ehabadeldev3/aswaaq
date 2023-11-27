<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\SubAccount;
use App\Traits\AccountTrait;
use App\Traits\Message;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubAccountController extends Controller
{
    use Message;
    use AccountTrait;


    public function __construct()
    {
        $this->middleware('permission:AccountsTree read', ['only' => ['index','getMainSub']]);
        $this->middleware('permission:AccountsTree create', ['only' => ['store']]);
        $this->middleware('permission:AccountsTree edit', ['only' => ['update', 'edit']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$main,SubAccount $subAccount)
    {
        $subAccounts = SubAccount::where([['main_account_id',$main],['sub_account_id',$subAccount->id]])->with(['parent','mainAccount'])->when($request->search, function ($q) use ($request) {
            return $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhereRelation('parent','name','like','%'.$request->search.'%')
                ->orWhereRelation('mainAccount','name','like','%'.$request->search.'%');
        })->latest('sub_accounts.id')->paginate(15);

        $items = collect($subAccounts->items())->map(function($item,int $key){
            if($key == 0){
                $item['parent']['debit_amount'] = $this->debitAmount($item['parent']);
                $item['parent']['credit_amount'] = $this->creditAmount($item['parent']);
            }else{
                $item['debit_amount'] = $this->debitAmount($item);
                $item['credit_amount'] = $this->creditAmount($item);
            }
           return $item;
        });
        $subAccounts->setCollection($items);


        // $subAccounts[0]['parent']['debit_amount'] = $this->debitAmount($subAccounts[0]['parent']);
        // $subAccounts[0]['parent']['credit_amount'] = $this->creditAmount($subAccounts[0]['parent']);

        // foreach ($subAccounts as $index=>$subAccount)
        // {
        //     $subAccounts[$index]['debit_amount'] = $this->debitAmount($subAccount);
        //     $subAccounts[$index]['credit_amount'] = $this->creditAmount($subAccount);
        // }

        $data = $this->getSubAccountDataUsingRecursion($subAccount);

        // $data = [];
        // $sub = $sub_account;

        // $data[]=$sub;

        // $sub_account_id = $sub->sub_account_id;

        // for ($i = 1;$i<=10;$i++){
        //     $main = SubAccount::find($sub_account_id);
        //     if ($main){
        //         array_unshift($data,$main);
        //         if ($main->sub_account_id == null)
        //         {
        //             break;
        //         }
        //         $sub_account_id = $main->sub_account_id;
        //     }
        // }

        return $this->sendResponse(['subAccounts' => $subAccounts,'data' => $data], 'Data exited successfully');
    }

    public function getMainSub(SubAccount $subAccount){

        $data = $this->getSubAccountDataUsingRecursion($subAccount);

        // $sub = SubAccount::find($id);

        // $data[]=$sub;

        // $sub_account_id = $sub->sub_account_id;
        // for ($i = 1;$i<=10;$i++){
        //     $main = SubAccount::find($sub_account_id);
        //     if ($main){
        //         array_unshift($data,$main);
        //         if ($main->sub_account_id == null)
        //         {
        //             break;
        //         }
        //         $sub_account_id = $main->sub_account_id;
        //     }
        // }

        return $this->sendResponse(['data' => $data], 'Data exited successfully');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$main,$id)
    {
        $request->validate([
            'name' => ['required', Rule::unique('sub_accounts', 'name')],
            'debit' => 'required'
        ]);

        $data = $request->only(['name','main_account_id','debit']);
        $data['main_account_id'] = $main;
        $data['sub_account_id'] = $id;

        SubAccount::create($data);

        return $this->sendResponse([], 'Data exited successfully');
    }

    public function edit(SubAccount $subAccount)
    {
        $data = $this->getSubAccountDataUsingRecursion($subAccount);

        // $data=[];
        // $sub_account_id = $sub_account->sub_account_id;

        // for ($i = 1;$i<=10;$i++){
        //     $main = SubAccount::find($sub_account_id);
        //     if ($main){
        //         array_unshift($data,$main);
        //         if ($main->sub_account_id == null)
        //         {
        //             break;
        //         }
        //         $sub_account_id = $main->sub_account_id;
        //     }
        // }

        return $this->sendResponse(['income' => $subAccount, 'data' => $data], 'Data exited successfully');
    }

    
    public function update(Request $request,SubAccount $subAccount)
    {
      
        $data =  $request->validate([
            'name' => ['required', Rule::unique('sub_accounts', 'name')->whereNot('id', $subAccount->id)],
            'debit' => 'required'
        ]);

        $subAccount->update($data);
        return $this->sendResponse([], 'Data exited successfully');
    }



    protected function getSubAccountDataUsingRecursion(SubAccount $subAccount, array $data = [])
    {
        array_unshift($data, $subAccount);

        if ($subAccount->sub_account_id !== null) {
            $main = SubAccount::find($subAccount->sub_account_id);
            if ($main !== null) {
                return $this->getSubAccountDataUsingRecursion($main, $data);
            }
        }

        return $data;
    }

}
