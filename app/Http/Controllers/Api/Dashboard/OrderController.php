<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Api\MobileApp\MyfatoorahController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\OrderTakeActionRequest;
use App\Models\Order;
use App\Models\OrdersGroup;
use App\Models\Setting;
use App\Traits\AfterConfirmedOrderStatusUtility;
use App\Traits\AfterOrderGetCanceledOrFullReturnUtility;
use App\Traits\ChangeOrderStatusUtility;
use App\Traits\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    use Message,ChangeOrderStatusUtility,AfterConfirmedOrderStatusUtility,AfterOrderGetCanceledOrFullReturnUtility;

    public function __construct()
    {
        $this->middleware('permission:orderOnline read', ['only' => ['index','show']]);
        $this->middleware('permission:orderOnline edit', ['only' => ['updateOrderItem','deleteItemCard','take_action_to_orders','confirm_order_status','assignRepresentativeToOrder','assignCarToOrder']]);
    }

    //get all orders
    public function index(Request $request)
    {
        $orders = $this->search($request)->paginate($request->paginate ?? 25);
        $all_orders_for_excel = [];
        $total_amount = $this->search($request)->whereIn('order_status', ['Completed','Partial Return'])->where('confirmed_received_amount',1)->sum('total_amount');
        return $this->sendResponse(['orders' => $orders,'total_amount'=> number_format((float) $total_amount, 2, '.', '')  , 'all_orders_for_excel' => $all_orders_for_excel], 'Data exited successfully');
    }


    // order details
    public function show($id)
    {
        $order = Order::getSupplierOrdersIfDispatcherAuthenticated()->with('representative','notes','supplier','user','shop','notes','group','run_sheet')->findOrFail($id);
        $orders_group = Order::getSupplierOrdersIfDispatcherAuthenticated()->with('supplier')->where('id','!=',$id)->where('group_id',$order->group_id)->get();
        $products = $order->products->load(['product','product.flavor','product.size']);
        $client_orders = OrdersGroup::where('user_id', $order->user_id)->count();
        $supplier_orders = Order::getSupplierOrdersIfDispatcherAuthenticated()->where('supplier_id', $order->supplier_id)->count();
        $refund_allowed_days = Setting::find(1)->refund_allowed_days;
        $refund_allowed = $order->updated_at->addDays($refund_allowed_days) > now();
        $refund_date = $order->updated_at->addDays($refund_allowed_days)->format('Y-m-d / (H:i)');
        return $this->sendResponse(['order' => $order,'orders_group' => $orders_group, 'products' => $products,'supplier_orders' => $supplier_orders, 'refund_date' => $refund_date, 'refund_allowed' => $refund_allowed, 'order_numbers' => $client_orders, 'client_orders' => $client_orders], 'Data exited successfully');
    }


    public function take_action_to_orders(OrderTakeActionRequest $request){
        $orders = Order::getSupplierOrdersIfDispatcherAuthenticated()->find($request->orders);
        foreach( $orders as $order){
            if(!$order->confirmed_received_amount)
                $this->update_order_status_or_representative_or_car($order,$request,$request->order_status);
        }
    }

    public function confirm_order_status(Request $request){
        $request->validate(['orders' => 'required|array|min:1','orders.*' => 'exists:orders,id']);
        $number_of_failed_orders = 0;
        foreach($request->orders as $order_id){
            $order = Order::getSupplierOrdersIfDispatcherAuthenticated()->where('confirmed_received_amount',0)->whereIn('order_status',['Completed','Partial Return','Full Return','Canceled'])->find($order_id);
            if($order){
                 $order->update(['confirmed_by' => auth()->user()->id,'confirmed_received_amount' => 1]);
                 $this->returnPointToWalletAfterCanceledFullReturnAndPartialReturn($order);
                if($order->order_status != 'Canceled'){
                    $order->update(['payment_status' => 'Paid']);
                    $this->putOrderAmountInIncomes($order->total_amount,$order->user->name,['notes' => "مقابل شراء منتجات. رقم الطلب ($order->id) "]);
                    $this->calculatePointsForUserAfterConfirmOrderStatus($order->user);//check if the user achieved target or not
                    $this->calculateSupplierCommission($order);
                }
                //  if($order->order_status != 'Canceled')
                //     $this->putShippingCostForParentOrderForTheFirstTime($order->group);

            }else{
                $number_of_failed_orders++;
            }
        }

        return response()->json(['number_of_failed_orders' => $number_of_failed_orders]);
    }


    public function assignRepresentativeToOrder(Request $request)
    {
        if ($order=Order::getSupplierOrdersIfDispatcherAuthenticated()->find($request->order_id)) {
            if(($order->order_status == 'Pending' || $order->order_status == 'Shipping' || $order->order_status == 'Processing' ) && !$order->confirmed_received_amount){
                if($request->type == 'cancel')
                $order->update(['representative_id' => null]);
                else
                    $order->update(['representative_id' => $request->rep_id]);
                return response()->json([],200);
            }
        }
        return response()->json([],404);
    }


    public function assignCarToOrder(Request $request)
    {
        if ($order=Order::getSupplierOrdersIfDispatcherAuthenticated()->find($request->order_id)) {
            if(($order->order_status == 'Pending' || $order->order_status == 'Shipping' || $order->order_status == 'Processing' ) && !$order->confirmed_received_amount){
                if($request->type == 'cancel')
                $order->update(['car_id' => null]);
                else
                    $order->update(['car_id' => $request->car_id]);
                return response()->json([],200);
            }
        }
        return response()->json([],404);
    }


    public function updateOrderItem(Request $request)
    {
        $response = $this->update_stock_and_order_amount_after_change_order_status($request->order_id,$request->price_id,$request->measurement_id,$request->quantity);
        if(!is_integer($response))
            return $response;
        return response()->json(['message' => 'UpdatedSuccessfully'], 200);
    }

    public function deleteItemCard(Request $request)
    {
        $response = $this->update_stock_and_order_amount_after_change_order_status($request->order_id,$request->price_id,$request->measurement_id,0);
        if( $response != 1)
            return $response;
        return response()->json(['message' => 'DeletedSuccessfully'], 200);
    }


    public function saveNoteToOrder(Request $request, Order $order)
    {
        $data = $request->validate(['note' => 'required']);
        $data['user_id']=auth()->user()->id;
        $order->notes()->create($data);
        return response()->json(['message' => 'Data exited successfully'], 200);
    }



    // search
    protected function search($request)
    {
        return Order::with(['representative','shop','user','supplier','car','notes','run_sheet'])
        ->getSupplierOrdersIfDispatcherAuthenticated()
        ->where(function ($q) use ($request) {
                return $q
                    ->when($request->payment_status, function ($q) use ($request) {
                        return $q->where('payment_status', $request->payment_status);
                    });
            })
            ->where(function ($q) use ($request) {
                return $q
                    ->when($request->payment_method, function ($q) use ($request) {
                        return $q->where('payment_method', $request->payment_method);
                    });
            })
            ->where(function ($q) use ($request) {
                return $q
                    ->when($request->filter_confirmed_status == 'confirmed' || $request->filter_confirmed_status == 'non_confirmed', function ($q) use ($request) {
                        return $q->where('confirmed_received_amount', $request->filter_confirmed_status == 'confirmed' ? 1 : 0);
                    });
            })
            ->where(function ($q) use ($request) {
                return $q
                    ->when($request->order_status, function ($q) use ($request) {
                        if ($request->order_status == 'hold') {
                            return $q->where('hold', 1);
                        } else {
                            return $q->where('order_status', $request->order_status);
                        }
                    });
            })
            ->where(function ($q) use ($request) {
                $q->when($request->client_name, function ($q) use ($request) {
                    return $q->WhereRelation('user','name','like','%'.  $request->client_name.'%');
                });
            })
            ->where(function ($q) use ($request) {
                $q->when(is_numeric($request->order_number), function ($q) use ($request) {
                    return $q->Where('id',  $request->order_number);
                });
            })
            ->where(function ($q) use ($request) {
                $q->when(is_numeric($request->representative_id), function ($q) use ($request) {
                    return $q->Where('representative_id',  $request->representative_id);
                });
            })
            ->where(function ($q) use ($request) {
                $q->when(is_numeric($request->supplier_id), function ($q) use ($request) {
                    return $q->Where('supplier_id',  $request->supplier_id);
                });
            })
            ->where(function ($q) use ($request) {
                $q->when(is_numeric($request->area_id), function ($q) use ($request) {
                    return $q->Where('area_id',  $request->area_id);
                });
            })
            ->where(function ($q) use ($request) {
                $q->when($request->fromDate && $request->toDate, function ($q) use ($request) {
                    return $q->WhereDate('created_at','>=',$request->fromDate)->where('created_at','<=',$request->toDate);
                });
            })
            ->where(function ($q) use ($request) {
                $q->when($request->delivery_date, function ($q) use ($request) {
                    return $q->WhereDate('delivery_date',$request->delivery_date);
                });
            })

            ->latest('id');
    }

}
