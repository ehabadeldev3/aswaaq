<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\WalletTargetRequest;
use App\Models\ClientCategory;
use App\Models\User;
use App\Models\WalletTarget;
use App\Models\WalletTargetsClient;
use App\Traits\Message;
use Illuminate\Http\Request;

class WalletTargetController extends Controller
{
    use Message;

    public function __construct()
    {
        $this->middleware('permission:Wallet target read', ['only' => ['index','get_all_client_categories','get_all_clients']]);
        $this->middleware('permission:Wallet target create', ['only' => ['store']]);
        $this->middleware('permission:Wallet target edit', ['only' => ['update','edit']]);
        $this->middleware('permission:Wallet target delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $wallet_targets = WalletTarget::withCount('users')->
        where(function($q) use($request){
            $q->when($request->search, function ($q) use ($request) {
                return $q->where('amount', 'like', '%' . $request->search . '%')
                    ->orWhere('points', 'like', '%' . $request->search . '%');
            });
        })
        ->where(function($q) use($request){
            $q->when($request->date, function ($q) use ($request) {
                $q->whereDate('start_date' ,'<=' , $request->date)->whereDate('end_date' ,'>=' , $request->date);
            });
        })
        ->latest()->paginate(15);
        return $this->sendResponse(['wallet_targets' => $wallet_targets], 'Data exited successfully');
    }

    public function get_all_client_categories(Request $request)
    {
        $client_categories = ClientCategory::latest()->get();
        return $this->sendResponse(['client_categories' => $client_categories], 'Data exited successfully');
    }

    public function get_all_clients(Request $request)
    {
        $clients = [];
        if($request->selected_client_category && $client_category = ClientCategory::findOrFail($request->selected_client_category)){
            $clients = $this->all_clients()->whereBetween('subtotal',[$client_category->from_amount,$client_category->to_amount]);
        }

        return response()->json(['clients' => $clients]);
    }

    public function store(WalletTargetRequest $request)
    {
        $data = $request->only(['amount','points','start_date','end_date']);
        $wallet_target=WalletTarget::create($data);
        $clients = $request->clients;
        if($request->selected_client_category == 0){ //assign target to all clients
            $clients =User::select(['id'])->whereHas('client')->get()->pluck('id')->toArray();
        }
        foreach($clients as $client_id)
            $wallet_target->users()->syncWithoutDetaching([$client_id => ['points' =>$request->points]]);

        return $this->sendResponse([], 'Data exited successfully');
    }

    public function edit(WalletTarget $wallet_target)
    {
        $clients_ids = $wallet_target->users->pluck('id')->toArray();
        $all_clients = $this->all_clients();
        return $this->sendResponse(['wallet_target' => $wallet_target,'clients' => $clients_ids,'all_clients' => $all_clients], 'Data exited successfully');
    }

    protected function all_clients()
    {
        $clients = User::select(['id','name','phone'])->whereHas('client')->withSum(['orders as total_amount' => function ($query) {
            $query->whereIn('order_status' , ['Completed','Partial Return'])->where('confirmed_received_amount',1);
        }], 'subtotal')->latest()->get();

        return $clients;
    }


    public function update(WalletTargetRequest $request, WalletTarget $walletTarget)
    {
        $data = $request->only(['amount','points','start_date','end_date']);
        $walletTarget->update($data);
        WalletTargetsClient::where('wallet_target_id' , $walletTarget->id)->where('achieved',0)->delete();
        $clients = $request->clients;
        if($request->selected_client_category == 0 && count($clients) == 0){ //assign target to all clients
            $clients =User::select(['id'])->whereHas('client')->get()->pluck('id')->toArray();
        }
        foreach($clients as $client_id)
            $walletTarget->users()->syncWithoutDetaching([$client_id => ['points' =>$request->points]]);

        return $this->sendResponse([], 'Data exited successfully');

    }

    public function destroy(WalletTarget $walletTarget)
    {
        WalletTargetsClient::where('wallet_target_id' , $walletTarget->id)->delete();
        $walletTarget->delete();
        return $this->sendResponse([], 'Data exited successfully');
    }

}
