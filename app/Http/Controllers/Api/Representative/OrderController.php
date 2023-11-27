<?php

namespace App\Http\Controllers\Api\Representative;

use App\Http\Controllers\Api\Dashboard\OrderController as DashboardOrderController;
use App\Http\Controllers\Controller;
use App\Http\Resources\MobileResources\RepresentativeOrderResource;
use App\Models\Order;
use App\Traits\Message;
use Illuminate\Support\Facades\DB;
use App\Models\OrdersProduct;
use App\Models\Price;
use App\Models\User;
use App\Notifications\Admin\AddNotification;
use App\Traits\ChangeOrderStatusUtility;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use Message,ChangeOrderStatusUtility;

    // get Representative order start
    public function getRepresentativeOrder(){
        $orders = Order::
        where('representative_id',auth()->user()->id)
        ->whereIn('order_status',['Pending','Shipping','Processing'])
        ->whereNull('representative_refund_part_note')
        ->whereHold(0)
        ->with(['shop','products','products.product','products.product.flavor','products.product.size','products.product.mainMeasurementUnit','products.product.subMeasurementUnit','area','user'])->latest()
        ->get();

        return $this->sendResponse(['orders' => RepresentativeOrderResource::collection($orders)], trans('message.messageSuccessfully'));
    }


    public function orderDelivered(Request $request,$id){
        $response = $this->update_order_status($id,$request,'Completed');
        if($response['order'] && $response['check_changed_successfully']){
            $response['order']->update(['changed_by_representative' => auth()->user()->id]);
            return $this->sendResponse([], trans('message.messageSuccessfully'));
        }
        return $this->sendError('');
    }

    public function orderReturned(Request $request,$id){
        $request->validate($this->representative_note_validation_rule());
        $response = $this->update_order_status($id,$request,'Full Return');
        if($response['order'] && $response['check_changed_successfully']){
            $this->save_representative_note($response['order'],$request->representative_refund_note);
            return $this->sendResponse([], trans('message.messageSuccessfully'));
        }

        return $this->sendError('');
    }

    public function orderPartReturned(Request $request,$id){
        $request->validate(array_merge($this->representative_note_validation_rule(),['representative_refund_part_note' => 'required']));
        $order = Order::where('representative_id',auth()->user()->id)
                    ->whereIn('order_status',['Pending','Shipping','Processing'])
                    ->whereNull('representative_refund_part_note')->whereHold(0)->findOrFail($id);
        $order_controller = new DashboardOrderController();
        DB::beginTransaction();

        //validation
        foreach($request->representative_refund_part_note as $item){

            $order_price = OrdersProduct::where('order_id',$id)->where('price_id',$item['price_id'])->where('measurement_id',$item['measurement_id'])->first();
            $price = Price::findOrFail($item['price_id']);
            $order_group = $order->group;

            $available_quantity_in_stock =  $order_price->measurement_type == 'sub' ?$price->quantity : floor($price->quantity / $price->product->count_unit);

            //check in stock or out of stock
            if( $item['quantity'] - $order_price->quantity > $available_quantity_in_stock){
                DB::rollBack();
                return response()->json(['message' => __('message.Out of stock and the available quantity in stock is') . " " . $available_quantity_in_stock." " . $order_price->measurement->name,'item' => $item],404);
            }

            $order_controller->update_order_quantities_and_amount($item['quantity'], $order_price, $price, $order, $order_group);
        }

        DB::commit();

        $order_controller->update_order_status_or_representative_or_car($order,$request,'Partial Return');
        $this->save_representative_note($order,$request->representative_refund_note);
        $order->update([
            'representative_refund_part_note'=>json_encode($request->representative_refund_part_note),
        ]);
        $rep_name = auth()->user()->name;
        $client_name = $order->user->name ;
        $message = " العميل " . $client_name . " قام باٍسترجاع جزئي للطلب رقم " . $id . " مع المندوب " . $rep_name ;
        $message_en = "$client_name made a partial refund for order number $id with the delivery man called $rep_name";
        $image = asset('upload/representative_profile/'.auth()->user()->media->file_name);
        $this->sendNotification($id,$message,$image,$message_en);

        return $this->sendResponse([], trans('message.messageSuccessfully'));
    }


    public function sendNotification($id,$message,$image,$message_en){
        User::whereAuthId(1)
            ->whereRelation('roles.notify','name','Add OnlineOrder')
            ->each(function ($admin) use($id,$message,$image,$message_en){
                $admin->notify(new AddNotification('',$id,'showOrderOnline',$message,$image,$message_en));
            });
    }


    protected function update_order_status($order_id,$request,$order_status){
        $order = Order::
        where('representative_id',auth()->user()->id)
        ->whereIn('order_status',['Pending','Shipping','Processing'])
        ->whereNull('representative_refund_part_note')
        ->whereHold(0)
        ->whereId($order_id)
        ->first();

        $change_status_controller = new OrderController();
        $check_changed_successfully = $change_status_controller->update_order_status_or_representative_or_car($order,$request,$order_status);

        return ['order' => $order , 'check_changed_successfully' => $check_changed_successfully];
    }

    protected function save_representative_note($order,$note){
        $data['user_id']=auth()->user()->id;
        $data['type']='representative';
        $data['note']=$note;
        $order->notes()->create($data);
        $order->update(['changed_by_representative' => auth()->user()->id]);
        return 1;
    }

    protected function representative_note_validation_rule(){
        return [
            'representative_refund_note' => 'required|in:The place is closed,The order is fake,The place is closed,There is not enough cash,The product is different from the description,The expiry date is invalid,The products were ordered by mistake from the representative,The products were ordered by mistake from the customer'
        ];
    }
}
