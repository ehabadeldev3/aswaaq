<?php

namespace App\Http\Controllers\Api\MobileApp;

use App\Http\Controllers\Api\Dashboard\OrderController;
use App\Http\Controllers\Controller;
use App\Models\IncomeAndExpense;
use App\Models\OrdersGroup;
use App\Models\User;
use App\Notifications\Admin\AddNotification;
use Illuminate\Http\Request;

class MyfatoorahController extends Controller
{

    public function pay_online_by_myfatoorah($invoice_value,$user,$refrence_id){
        $callback_success = route('pay_with_myfatoorah_callback_success');
        $callback_error = route('pay_with_myfatoorah_callback_error');
        $response = $this->index($invoice_value,$user->name,$user->phone,$user->email,$refrence_id,'+2',$callback_success,$callback_error);
        if(isset($response->IsSuccess) && $response->IsSuccess == 'true'){
            OrdersGroup::findOrFail($refrence_id)->update(['invoice_id' => $response->Data->InvoiceId]);
            return $response->Data->InvoiceURL;
        }
        return 0;
    }


    public function index($invoice_value,$user_name,$user_phone,$user_email,$reference_id,$country_code,$callback_success,$callback_error){

        //Fill POST fields array
        $postFields = $this->postFields($invoice_value,$user_name,$user_phone,$user_email,$reference_id,$country_code,$callback_success,$callback_error);

        //Call endpoint
        $data = $this->callAPI("/v2/SendPayment",$postFields);

        // //You can save payment data in database as per your needs
        // $invoiceId   = $data->InvoiceId;
        // $paymentLink = $data->InvoiceURL;

        return $data;
    }

    public function callAPI($endpointURL,  $postFields = [], $requestType = 'POST')
    {
        $api_url="https://apitest.myfatoorah.com";
        $key="rLtt6JWvbUHDDhsZnfpAhpYk4dxYDQkbcPTyGaKp2TYqQgG7FGZ5Th_WD53Oq8Ebz6A53njUoo1w3pjU1D4vs_ZMqFiz_j0urb_BH9Oq9VZoKFoJEDAbRZepGcQanImyYrry7Kt6MnMdgfG5jn4HngWoRdKduNNyP4kzcp3mRv7x00ahkm9LAK7ZRieg7k1PDAnBIOG3EyVSJ5kK4WLMvYr7sCwHbHcu4A5WwelxYK0GMJy37bNAarSJDFQsJ2ZvJjvMDmfWwDVFEVe_5tOomfVNt6bOg9mexbGjMrnHBnKnZR1vQbBtQieDlQepzTZMuQrSuKn-t5XZM7V6fCW7oP-uXGX-sMOajeX65JOf6XVpk29DP6ro8WTAflCDANC193yof8-f5_EYY-3hXhJj7RBXmizDpneEQDSaSz5sFk0sV5qPcARJ9zGG73vuGFyenjPPmtDtXtpx35A-BVcOSBYVIWe9kndG3nclfefjKEuZ3m4jL9Gg1h2JBvmXSMYiZtp9MR5I6pvbvylU_PP5xJFSjVTIz7IQSjcVGO41npnwIxRXNRxFOdIUHn0tjQ-7LwvEcTXyPsHXcMD8WtgBh-wxR8aKX7WPSsT1O8d8reb2aR7K3rkV3K82K_0OgawImEpwSvp9MNKynEAJQS6ZHe_J_l77652xwPNxMRTMASk1ZsJL";
        $curl = curl_init($api_url . $endpointURL);
        curl_setopt_array($curl, array(
            CURLOPT_CUSTOMREQUEST  => $requestType,
            CURLOPT_POSTFIELDS     => json_encode($postFields),
            CURLOPT_HTTPHEADER     => array("Authorization: Bearer $key", 'Content-Type: application/json'),
            CURLOPT_RETURNTRANSFER => true,
        ));
        $response = curl_exec($curl);
        $curlErr  = curl_error($curl);
        curl_close($curl);
        $error = $this->handleError($response);
        if ($error || $curlErr) {
            return $error;
        }
        return json_decode($response);
    }



    private function postFields($invoice_value,$user_name,$user_phone,$user_email,$reference_id,$country_code,$callback_success,$callback_error){
        return [
            //Fill required data
            'NotificationOption' => 'SMS', //'SMS', 'EML', or 'ALL'
            'InvoiceValue'       => $invoice_value,
            'CustomerName'       => $user_name,
                //Fill optional data
                'DisplayCurrencyIso' => 'kwd',
                'MobileCountryCode'  => $country_code,
                'CustomerMobile'     => $user_phone,
                // 'CustomerEmail'      => "admin@admin.com",
                'CallBackUrl'        => $callback_success,
                'ErrorUrl'           => $callback_error, //or 'https://example.com/error.php'
                'Language'           => app()->getLocale(), //or 'ar'
                'CustomerReference'  => $reference_id,
                //'CustomerCivilId'    => 'CivilId',
                //'UserDefinedField'   => 'This could be string, number, or array',
                //'ExpiryDate'         => '', //The Invoice expires after 3 days by default. Use 'Y-m-d\TH:i:s' format in the 'Asia/Kuwait' time zone.
                //'SourceInfo'         => 'Pure PHP', //For example: (Laravel/Yii API Ver2.0 integration)
                //'CustomerAddress'    => $this->customerAddress($block,$street,$building_no,$address,$address_instruction),
                //'InvoiceItems'       => $this->invoiceItems($items),
        ];
    }


    private function handleError($response)
    {

        $json = json_decode($response);
        if (isset($json->IsSuccess) && $json->IsSuccess == true) {
            return null;
        }
        if (isset($json->ValidationErrors) || isset($json->FieldsErrors)) {
            $errorsObj = isset($json->ValidationErrors) ? $json->ValidationErrors : $json->FieldsErrors;
            $blogDatas = array_column($errorsObj, 'Error', 'Name');

            $error = implode(', ', array_map(function ($k, $v) {
                return "$k: $v";
            }, array_keys($blogDatas), array_values($blogDatas)));
        } else if (isset($json->Data->ErrorMessage)) {
            $error = $json->Data->ErrorMessage;
        }

        if (empty($error)) {
            $error = (isset($json->Message)) ? $json->Message : (!empty($response) ? $response : 'API key or API URL is not correct');
        }

        return $error;
    }


    public function pay_with_myfatoorah_callback_success(Request $request)
    {

        if(isset($request['paymentId'])){
            $myfatorah = new MyFatoorahController();
            $postFields=[
                'Key' => $request['paymentId'],
                'KeyType' => 'PaymentId',
            ];
            $data = $myfatorah->callAPI("/v2/GetPaymentStatus",$postFields);
            $transaction = collect($data->Data->InvoiceTransactions)->where('TransactionStatus','Succss')->first();
            if(isset($data->Data->CustomerReference) && $transaction){
                $order_group = OrdersGroup::findOrFail($data->Data->CustomerReference);
                $user = $order_group->user;
                $order_group->update(['payment_status' => 'Paid' ,'transaction_id' => $transaction->TransactionId]);
                $order_group->orders()->update(['payment_status' => 'Paid']);
                $user->notify(new AddNotification('', $order_group->orders()->first()->id, 'showOrderOnline', "تم الدفع بنجاح" , $user->image,"Payment completed successfully"));
                $sub_orders_ids = implode(' , ',$order_group->orders->pluck('id')->toArray());
                $this->sendNotification($order_group->orders()->first()->id,"لقد تم الدفع اونلاين لهذة الطلبات $sub_orders_ids",$user->image,"Online payment for these orders ($sub_orders_ids) has been paid");
                // $this->putOrderAmountInIncomes($order_group->total_amount,$user->name,$sub_orders_ids);
                return view('success_payment')->with([
                    'user_name' => $user->name,
                    'invoice_id' => $order_group->invoice_id,
                    'transaction_id' => $order_group->transaction_id,
                    'total_amount' => $order_group->total_amount,
                    'date' => now()->format('Y-m-d (H:i)'),
                ]);
            }
        }
    }

    protected function putOrderAmountInIncomes($amount ,$name,$orders_ids)
    {
        IncomeAndExpense::create([
            'amount' => $amount,
            'payment_date' => now()->format('Y-m-d'),
            'payer' => $name,
            'note' => "لقد قام $name بالدفع اونلاين لهذة الطلبات $orders_ids",
            'treasury_id' =>1,
            'income_id' =>1,
        ]);
    }
    protected function sendNotification($id, $message, $image,$message_en)
    {
        User::whereAuthId(1)
            ->whereRelation('roles.notify', 'name', 'Online Payment')
            ->each(function ($admin) use ($id, $message, $image,$message_en) {
                $admin->notify(new AddNotification('', $id, 'showOrderOnline', $message, $image,$message_en));
            });
    }
    public function pay_with_myfatoorah_callback_error(Request $request)
    {
        if(isset($request['paymentId'])){
            $myfatorah = new MyFatoorahController();
            $postFields=[
                'Key' => $request['paymentId'],
                'KeyType' => 'PaymentId',
            ];
            $data = $myfatorah->callAPI("/v2/GetPaymentStatus",$postFields);
            if(isset($data->Data->CustomerReference) && collect($data->Data->InvoiceTransactions)->last()->TransactionStatus == "Failed"){
                $orders_groups = OrdersGroup::findOrFail($data->Data->CustomerReference);
                $orders_groups->update(['payment_status'=>'Failed']);
                $order_controller = new OrderController();
                foreach($orders_groups->orders as $order){
                    $order_controller->update_order_status_or_representative_or_car($order,$request,'Canceled');
                }
                return view('failed_payment')->with([
                    'user_name' => $orders_groups->user->name,
                    'invoice_id' => $orders_groups->invoice_id,
                    'date' => now()->format('Y-m-d (H:i)'),
                ]);
            }
        }
    }
}
