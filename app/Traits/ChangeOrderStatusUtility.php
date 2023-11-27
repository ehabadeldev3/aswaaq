<?php

namespace App\Traits;

use App\Models\Discount;
use App\Models\Order;
use App\Models\OrdersProduct;
use App\Models\Price;
use App\Models\User;
use App\Notifications\Admin\AddNotification;

trait ChangeOrderStatusUtility
{
    use NotificationTrait,AfterOrderGetCanceledOrFullReturnUtility;
    public function update_order_status_or_representative_or_car($order,$request,$order_status){
        $order_id = $order->id ;
        $user = User::find($order->user_id);
        $message_en = '';
        $message_ar = '';
        if($order ){//&& !in_array($order->order_status,['Completed','Canceled','Partial Return','Full Return'])

            if($request->representative_id)
                $order->update(['representative_id' => $request->representative_id]);

            if($request->car_id)
                $order->update(['car_id' => $request->car_id]);

            if($order_status && $order_status != 'Hold'){
                $this->cancelAndFullReturnStatusAction($order_status,$order);
                $order->update(['order_status' => $order_status,'hold' => 0]);
                $this->calculate_coupon_discount_after_any_returned_any_amount_of_quantities($order);
                $messages = $this->getMessagesArabicAndEnglishForNotifications($order_status,$order_id);
                $message_ar = $messages['message_ar'];
                $message_en = $messages['message_en'];
            }
            if($order_status && $order_status == 'Hold'){
                $message_ar = $order->hold ? " $order_id جاري استكمال الطلب رقم " :"مؤقتا $order_id تم وقف الطلب رقم " ;
                $message_en = $order->hold ? 'Order Number ('. $order_id.') is processing now' :'Order Number ('. $order_id.') is holding now' ;
                $order->update(['hold' => $order->hold ? 0 : 1]);
            }
            if($order_status && $message_ar && $message_en)
                $this->notifiy($user,$order_id,$message_en,$message_ar);

            $order->save();

            $this->worst_case_for_shipping_cost($order);
            return 1 ;
        }

        return 0 ;

    }

    public function update_stock_and_order_amount_after_change_order_status($order_id,$price_id,$measurement_id,$request_quantity = 0){
        $order = Order::getSupplierOrdersIfDispatcherAuthenticated()->findOrFail($order_id);
        $order_price = OrdersProduct::where('order_id',$order_id)->where('price_id',$price_id)->where('measurement_id',$measurement_id)->first();
        $price = Price::findOrFail($price_id);
        $order_group = $order->group;

        $available_quantity_in_stock =  $order_price->measurement_type == 'sub' ?$price->quantity : floor($price->quantity / $price->product->count_unit);

        //check in stock or out of stock
        if( $request_quantity - $order_price->quantity > $available_quantity_in_stock)
            return response()->json(['message' => __('message.Out of stock and the available quantity in stock is') . " " . $available_quantity_in_stock." " . $order_price->measurement->name],404);


        $this->update_order_quantities_and_amount($request_quantity, $order_price, $price, $order, $order_group);

        $this->calculate_coupon_discount_after_any_returned_any_amount_of_quantities($order);
        return 1 ;

    }

    public function update_order_quantities_and_amount($request_quantity, $order_price, $price, $order, $order_group){
        $diff_in_quantity =  $request_quantity  - $order_price->quantity ;
        $dif_in_amount = $diff_in_quantity * $order_price->price;
        $tax_percentage = $order_group->orderTax->sum('percentage');
        $old_tax_amount = $order->tax_amount;
        $new_tax_amount = ( $order->subtotal + $dif_in_amount) * $tax_percentage /100;
        $diff_in_tax =  $old_tax_amount - $new_tax_amount;
        $price->update([
            'quantity' =>$price->quantity - ($order_price->measurement_type == 'sub' ? $diff_in_quantity : $diff_in_quantity* $price->product->count_unit),
        ]);

        $order_price->update([
            'returned_quantity' => $order_price->returned_quantity - $diff_in_quantity > 0 ?$order_price->returned_quantity - $diff_in_quantity : 0,
            'refund_amount' => $order_price->refund_amount - $dif_in_amount > 0 ? $order_price->refund_amount - $dif_in_amount : 0,
            'sub_total' => $order_price->sub_total + $dif_in_amount,
            'quantity' => $order_price->quantity + $diff_in_quantity
        ]);

        $order->update([
            'total_amount' => $order->total_amount + $dif_in_amount - $diff_in_tax,
            'subtotal' => $order->subtotal + $dif_in_amount,
            'tax_amount' => $new_tax_amount,
            'refund_amount' => $order->refund_amount - $dif_in_amount > 0 ?$order->refund_amount - $dif_in_amount : 0
        ]);
        $order_group->update([
            'total_amount' => $order_group->total_amount + $dif_in_amount - $diff_in_tax,
            'subtotal' => $order_group->subtotal + $dif_in_amount,
            'tax_amount' => $order_group->tax_amount - $diff_in_tax,
            'refund_amount' => $order_group->refund_amount - $dif_in_amount > 0 ? $order_group->refund_amount - $dif_in_amount : 0
        ]);
        $order->save();
        $order_group->save();
    }


    //store notification to database -- and send it to the client by firebase
    public function notifiy($user,$order_id,$message_en,$message_ar)
    {
        User::whereAuthId(1)
        ->whereRelation('roles.notify','name','update order')
        ->each(function ($admin) use($order_id,$message_ar,$message_en,$user){
            $admin->notify(new AddNotification('',$order_id,'showOrderOnline',$message_ar,$user->image_path,$message_en));
        });
        $user->notify(new AddNotification('',$order_id,'showOrderOnline',$message_ar,'',$message_en));
        $this->send_firebase_notification_to_client("تعديل حالة الطلب رقم $order_id", $message_ar , $user->client->firebase_token);
    }

    protected function send_firebase_notification_to_client($title,$body,$client_firebase_token){
        $this->notification([$client_firebase_token], $body, $title, "send notification" ,"");
    }

    public function worst_case_for_shipping_cost($order) // if i canceled all sub orders and changed any one of them again to Pending or Processing or Shipping
    {
        $order_group = $order->group;
        $can_add_shipping_cost_to_this_order = $order_group->orders()->sum('shipping_cost') == 0 && $order_group->shipping_cost > 0 && in_array($order->order_status,['Pending','Processing','Shipping']);
        if($can_add_shipping_cost_to_this_order){
            $order->update(['shipping_cost' => $order_group->shipping_cost , 'total_amount' => $order->total_amount + $order_group->shipping_cost]);
        }
    }

    //calculate discount for the orders after any change on them
    protected function calculate_coupon_discount_after_any_returned_any_amount_of_quantities($order){
        $group = $order->group;
        $user = $order->user;
        $order_coupon_discount = $group->orderDiscount;

        if($order_coupon_discount && in_array($order->order_status,['Canceled','Full Return','Partial Return'])){
            $successful_orders = $group->orders()->whereNotIn('order_status',['Canceled','Full Return'])->get();
            $purchase_amount = $successful_orders->sum('subtotal');
            $coupon_discount =$order_coupon_discount->coupon()->withTrashed()->first();
            $next_processing_order = $successful_orders->where('order_status','!=','Completed')->where('order_status','!=','Partial Return')->sortBy('delivery_date')->first();

            if($coupon_discount->discount_value($purchase_amount)){// still has the coupon (get the discount amount for canceled , full return orders and distribute them to other orders)

                if(in_array($order->order_status,['Canceled','Full Return'])){ // add coupon discount for this order to another processing order if this order get canceled or full returned
                    if($next_processing_order){//if there is next order then add the discount to it
                        $total_amount_after_subtract_discount = $next_processing_order->total_amount - $order->coupon_discount > 0? $next_processing_order->total_amount - $order->coupon_discount : 0;
                        $remaining_amount_returned_to_wallet =$order->coupon_discount -$next_processing_order->total_amount;

                        if($remaining_amount_returned_to_wallet>0){ // if there is remaining amount after add the discount to the next processing order add the remaining to the user wallet
                            $user->update(['wallet_points' => $user->wallet_points  + $remaining_amount_returned_to_wallet]);
                            $user->notify(new AddNotification('', $order->id, 'showOrderOnline', "تم اضافة $remaining_amount_returned_to_wallet نقطة قيمة الباقي من الخصم علي الطلب رقم $order->id بعد اضافة قيمة الخصم علي طلبك التالي رقم الطلب $next_processing_order->id الي محفظتك" , $user->image_path,"$order->coupon_discount Points have been added to your wallet for the remaining discount of the order number $order->id after added the discount to your next processing order number $next_processing_order->id"));
                        }

                        $next_processing_order->update(['coupon_discount' => $next_processing_order->coupon_discount + $order->coupon_discount,'total_amount' => $total_amount_after_subtract_discount]);
                    }else{ //if this is the last sub order and get canceled or full return then  add the discount to the user wallet
                        $user->update(['wallet_points' => $user->wallet_points + $order->coupon_discount]);

                        $user->notify(new AddNotification('', $order->id, 'showOrderOnline', "تم اضافة $order->coupon_discount نقطة قيمة الخصم علي الطلب رقم $order->id الي محفظتك" , $user->image_path,"$order->coupon_discount Points have been added to your wallet for the discount of the order number $order->id"));
                    }

                    $order->update([ 'coupon_discount' => 0]);

                }

            }else{// now the user doesn't deserve the coupon discount

                $taken_discount_amount = $successful_orders->where('confirmed_received_amount',1)->whereIn('order_status',['Completed','Partial Return'])->sum('coupon_discount');
                if($next_processing_order){
                    $not_completed_orders = $successful_orders->whereIn('order_status',['Pending','Processing','Shipping']);
                    foreach($not_completed_orders as $order){
                        $order->update(['total_amount' => $order->total_amount  + $order->coupon_discount , 'coupon_discount' => 0]);
                    }

                    $order->update(['coupon_discount' => 0]);
                    $next_processing_order->update(['total_amount' => $next_processing_order->total_amount +$taken_discount_amount ,'coupon_discount' => $next_processing_order->coupon_discount -  $taken_discount_amount]);
                }else{
                    if(in_array($order->order_status,['Full Return','Partial Return'])){
                        $total_amount  = $order->total_amount + $order->coupon_discount + $taken_discount_amount;
                        $order->update(['total_amount' => $total_amount ,'coupon_dicount' => 0 - $taken_discount_amount,'refund_amount' => $order->refund_amount - $total_amount]);
                    }
                }
            }
        }

    }


    protected function getMessagesArabicAndEnglishForNotifications($order_status , $order_id){
        $message_ar ='';
        $message_en ='';

        switch($order_status){
            case 'Processing' :
                $message_ar = " $order_id جاري تجهيز الطلب رقم ";
                $message_en = 'Order Number ('. $order_id.') is processing now' ;
                break;
            case 'Shipping' :
                $message_ar = " $order_id جاري شحن الطلب رقم ";
                $message_en = 'Order Number ('. $order_id.') is Shipping now';
                break;
            case 'Completed' :
                $message_ar = " $order_id تم تسليم الطلب رقم ";
                $message_en = 'Order Number ('. $order_id.') has been completed';
                break;
            case 'Canceled' :
                $message_ar = " $order_id تم الغاء الطلب رقم ";
                $message_en = 'Order Number ('. $order_id.') has been canceled';
                break;
            case 'Full Return' :
                $message_ar = " $order_id تم ارجاع الطلب رقم ";
                $message_en = 'Order Number ('. $order_id.') has been returned';
                break;
            case 'Partial Return' :
                $message_ar = " $order_id تم تسليم جزئي الطلب رقم ";
                $message_en = 'Order Number ('. $order_id.') has been delivered partially';
                break;

        }

        return ['message_ar' => $message_ar,'message_en' => $message_en];
    }
}
