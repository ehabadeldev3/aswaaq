<?php

namespace App\Traits;

use App\Http\Controllers\Api\MobileApp\MyfatoorahController;
use App\Models\DailyOrdersLog;
use App\Models\Order;
use App\Models\OrdersProduct;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Subtotal;

trait AfterOrderGetCanceledOrFullReturnUtility
{

    protected function cancelAndFullReturnStatusAction($order_status, $order)
    {
        $refund_allowed_days_check = true;
        // $refund_allowed_days_check = $order->updated_at->addDays(Setting::first()->refund_allowed_days) > now();
        if ($order && $order->payment_status != 'Failed' && $refund_allowed_days_check && ($order_status == 'Canceled' || $order_status == 'Full Return')) {

            $this->returnProductToStock($order);

            $order_status == 'Canceled' ?
                $this->transfer_shipping_cost_to_next_order_if_the_current_order_get_canceled($order, $order->shipping_cost) :
                $this->refund_order_with_shipping_cost($order);

            $order->update(['payment_status' => 'Failed']);

            if ($order->payment_method == 'Online payment' && $order->payment_status == 'Paid' && $this->makeRefundRequest($order->refund_amount, $order->invoice_id)) {
                $order->update(['order_status' => 'Full Return']);
                return response()->json(['message' => 'Order Updated Successfully'], 200);
            }
            return response()->json(['message' => 'Order Canceled Successfully', 'canceled' => true], 200);
        }
    }

    //cancel order
    protected function returnProductToStock($order)
    {
        foreach ($order->products()->get() as $price) {
            $order_product = OrdersProduct::where('order_id', $order->id)->where('price_id', $price->id)->where('measurement_id', $price->pivot->measurement_id)->first();
            if ($order_product->quantity > 0 && $order_product->sub_total > 0) {

                $order_product->update(['quantity' => 0, 'returned_quantity' => $order_product->quantity, 'sub_total' => 0, 'refund_amount' => $order_product->sub_total]);
                $price->update(['quantity' => $price->quantity + ($price->pivot->measurement_type == 'sub' ? $price->pivot->quantity : $price->pivot->quantity * $price->product->count_unit)]);

                $product_log = DailyOrdersLog::whereDate('date', $order->created_at)
                    ->where('product_id', $price->product_id)
                    ->where('measurement_id', $price->pivot->measurement_id)
                    ->first();
                $deficit_after_cancel_or_refund_order = $product_log->deficit - $price->pivot->quantity;
                $product_log->update([
                    'quantity' => $product_log->quantity - $price->pivot->quantity,
                    'deficit' =>  $deficit_after_cancel_or_refund_order > 0 ? $deficit_after_cancel_or_refund_order : 0,
                ]);
            }
        }
    }


    protected function makeRefundRequest($amount, $invoice_id)
    {
        $postFields = [
            'Key' => $invoice_id,
            'KeyType' => 'InvoiceId',
            'Amount' => $amount
        ];
        $my_fatoorah_controller  = new MyfatoorahController();
        $data = $my_fatoorah_controller->callAPI("/v2/MakeRefund", $postFields);
        if (isset($data->IsSuccess) && $data->IsSuccess) {
            return true;
        }
        return false;
    }

    protected function transfer_shipping_cost_to_next_order_if_the_current_order_get_canceled($order, $shipping_cost)
    {
        $next_order = Order::where('group_id', $order->group_id)->whereIn('order_status', ['Pending', 'Processing', 'Shipping'])->orderBy('delivery_date')->first();
        if ($next_order) {
            $next_order->update(['total_amount' => $next_order->total_amount + $shipping_cost, 'shipping_cost' => $shipping_cost]);
            $order->update(['shipping_cost' => 0,'total_amount' =>  0, 'subtotal' =>  0, 'tax_amount' =>  0, 'refund_amount' => $order->total_amount - $shipping_cost]);
        }
    }

    protected function refund_order_with_shipping_cost($order)
    {
        $order->update(['total_amount' =>  0, 'subtotal' =>  0, 'tax_amount' =>  0, 'refund_amount' => $order->total_amount]);

        if ($order->shipping_cost > 0) {
            $order->update(['total_amount' =>  $order->shipping_cost, 'refund_amount' => $order->refund_amount - $order->shipping_cost]);
        }
    }
}
