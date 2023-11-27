<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\DailyOrdersLog;
use App\Models\User;
use App\Traits\Message;
use Illuminate\Http\Request;

class CollectOrdersByDateController extends Controller
{
    use Message;
    public function __construct()
    {
        $this->middleware('permission:CollectOrdersPerDay read', ['only' => ['collect_order_by_date','collect_orders_per_day_for_each_client']]);
        $this->middleware('permission:CollectOrdersPerDay edit', ['only' => ['updateDeficitForProduct']]);
    }
   

    public function collect_order_by_date(Request $request)
    {
        $date = $request->date ?? now();
        $products = DailyOrdersLog::collectOrdersPerDay($date)->paginate(500);
        $products->setCollection(collect($products->items())->groupBy('log_id'));
        return response()->json(['products' => $products]);
    }

    public function collect_orders_per_day_for_each_client(Request $request)
    {
        $date = $request->date ?? now()->format('Y-m-d');
        $users = User::
        whereRelation('orders','created_at','like',"%$date%")
        ->with(['orders' => function($q) use($date) {
            $q->where('created_at','like',"%$date%");
        },'orders.products','orders.products.product','orders.supplier','orders.products.product.flavor','orders.products.product.size'])->latest()->paginate(10);
        return response()->json(['users' => $users]);
    }

    public function updateDeficitForProduct(Request $request)
    {
        $request->validate(['log_id' => 'required|exists:daily_orders_logs,id','deficit' => 'required|numeric|gte:0']);
        DailyOrdersLog::find($request->log_id)->update(['deficit' => $request->deficit]);
        return response()->json([],200);
    }
}
