<?php

namespace App\Traits;

use App\Models\IncomeAndExpense;
use App\Models\WalletTargetsClient;
use App\Notifications\Admin\AddNotification;

trait AfterConfirmedOrderStatusUtility
{

    public function calculatePointsForUserAfterConfirmOrderStatus($user){
        foreach($user->active_targets() as $target){
            //total amount for user orders between each target start and end date
            $orders = $user->completedOrdersbetweenTargetDates($target->start_date,$target->end_date);

            $total_amount = $orders->get()->sum('total_amount');
            if($total_amount >= $target->amount){
                $user->update(['wallet_points' => $user->wallet_points + $target->points]);
                WalletTargetsClient::where('user_id',$user->id)->where('wallet_target_id',$target->id)->update(['achieved' =>1]);
                $orders->update(['wallet_target_id' => $target->id]);
            }
        }
    }
    public function calculateSupplierCommission($order){
        if($order->subtotal){
            $supplier = $order->supplier;
            $supplier_commission = round($order->subtotal * $supplier->commission_percentage / 100,2);
            $order->update([
                'commission_percentage' => $supplier->commission_percentage,
                'commission' => $supplier_commission,
            ]);
        }
    }

    //
    public function putShippingCostForParentOrderForTheFirstTime($order_group){

        // if(!$order_group->orders()->where('confirmed_received_amount',1)->whereIn('order_status' , ['Partial Return','Full Return','Completed'])->first()){
            // $sub_orders_ids = implode(' , ',$order_group->orders->pluck('id')->toArray());
            // $this->putOrderAmountInIncomes($order_group->shipping_cost,$order_group->user->name,['notes' => "مقابل تكلفة الشحن. لهذة الطلبات  ($sub_orders_ids) "]);
        // }

    }

    public function putOrderAmountInIncomes($amount ,$name,$arr_type)
    {
        if($amount > 0 )
        IncomeAndExpense::create(array_merge([
            'amount' => $amount,
            'income_id' =>3,
            'payment_date' => now()->format('Y-m-d'),
            'payer' => $name,
            'treasury_id' =>1,
        ]
        ,$arr_type));
    }


    protected function returnPointToWalletAfterCanceledFullReturnAndPartialReturn($order){
        $user = $order->user;
        $returned_points_to_wallet = 0 ;
        if($order->order_status == 'Canceled' || $order->order_status == 'Full Return'){
            $returned_points_to_wallet = $user->wallet_points;
            $user->update(['wallet_points' => $order->used_points + $returned_points_to_wallet]);
            $order->update(['used_points' => 0,'total_amount' => $order->total_amount + $returned_points_to_wallet]);
            $order->group->update(['used_points' => $order->group->used_points - $returned_points_to_wallet,'total_amount' => $order->group->total_amount + $returned_points_to_wallet]);
        }

        if(($order->order_status == 'Partial Return' || $order->order_status == 'Completed' ) && $order->refund_amount > 0 && $order->used_points > 0){
            $actual_used_points = $order->used_points - $order->refund_amount >= 0 ? $order->used_points - $order->refund_amount :0;
            $returned_points_to_wallet = $order->used_points - $actual_used_points;
            $user->update(['wallet_points' => $user->wallet_points + $returned_points_to_wallet]);
            $order->update(['used_points' => $actual_used_points,'refund_amount' => $order->refund_amount  - $returned_points_to_wallet,'total_amount' => $order->total_amount + $returned_points_to_wallet]);
            $order->group->update(['used_points' => $order->group->used_points - $returned_points_to_wallet,'total_amount' => $order->group->total_amount + $returned_points_to_wallet]);

        }

        if($returned_points_to_wallet > 0)
            $user->notify(new AddNotification('', $order->id, 'showOrderOnline', "تم ارجاع $returned_points_to_wallet نقطة الي المحفظة" , $user->image_path,"$returned_points_to_wallet Points have been returned to your wallet"));
    }


}
