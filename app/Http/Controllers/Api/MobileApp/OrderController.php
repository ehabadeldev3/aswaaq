<?php

namespace App\Http\Controllers\Api\MobileApp;

use App\Http\Controllers\Controller;
use App\Http\Resources\MobileResources\OrderGroupResource;
use App\Http\Resources\MobileResources\WalletTargetResource;
use App\Models\DailyOrdersLog;
use App\Models\Discount;
use App\Models\Order;
use App\Models\OrdersGroup;
use App\Models\OrderTax;
use App\Models\Price;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\SupplierDay;
use App\Models\User;
use App\Notifications\Admin\AddNotification;
use App\Traits\Message;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    use Message;

    public $current_shop_area_id;
    public function __construct()
    {
        $this->current_shop_area_id = current_shop_area_id();
    }

    public function get_date_and_cart_validation(Request $request)
    {
        $request->validate($this->rulesValidation());
        $data = $this->cart_process($request->cart);
        $this->get_cart_proccess_with_delivery_date_for_each_supplier($data);
        $dates = collect($data)->collapse()->pluck('delivery_date')->toArray(); // get all delivary dates
        $data['last_date'] =  Carbon::parse(now()->format('Y-m-d'))->diffInDays(Carbon::parse(max($dates))); // number of days
        return response()->json($data);
    }
    //create order
    public function createOrder(Request $request)
    {

        $request->validate($this->rulesValidation());

        $setting = Setting::first();

        $user = $request->user();
        $selected_shop = $user->client ? $user->client->selected_shop : 0;

        if(!$selected_shop)
            return $this->sendError(trans("message.You have to select shop first"));

        $data = $this->cart_process($request->cart);
        $this->get_cart_proccess_with_delivery_date_for_each_supplier($data);

        //check for  order amount limit
        if ($setting->order_amount > $data['total_amount'])
            return $this->sendError(trans("message.The total amount must be more than") . " " . $setting->order_amount);


        $coupon_data = $this->returnDiscountAndCoupon($request,$data['total_amount']);
        $discount=$coupon_data['discount'];
        $coupon=$coupon_data['coupon'];

        $shipping_price = $selected_shop->area->shipping_price ?? 0;
        $order_group = OrdersGroup::create([
            'payment_method' => $request->payment_method,
            'shop_id' => $selected_shop->id,
            'area_id' => $selected_shop->area_id,
            'user_id' => $user->id,
            'coupon_discount' => $discount,
            'coupon' => $discount && $coupon->code?$coupon->code:null,
            'payment_method' => $request->payment_method,
            'shipping_cost' => $shipping_price,
            'subtotal' => $data['total_amount'] ,
            'total_amount' => $data['total_amount'] - $discount,
        ]);
        foreach($data['success_cart_items'] as $key => $item){
            $supplier = Supplier::find($key);
            if($supplier){
                $order = Order::create([
                    'payment_method' => $request->payment_method,
                    'shop_id' => $selected_shop->id,
                    'group_id' => $order_group->id,
                    'area_id' => $selected_shop->area_id,
                    'user_id' => $user->id,
                    'supplier_id' => $key,
                    'payment_method' => $request->payment_method,
                    'subtotal' => $item['total_amount'],
                    'total_amount' => $item['total_amount'],
                    'delivery_date' => $item['delivery_date'],
                ]);

                //assoicate order with products
                $this->associateOrderWithProducts($order, $item);

                $order->save();
            }
        }
        $order_group->update(['total_amount' => $order_group->total_amount + $shipping_price]);
        $first_sub_order = $order_group->orders()->orderBy('delivery_date')->first();
        $first_sub_order->update(['total_amount' => $first_sub_order->total_amount + $shipping_price ,'shipping_cost' => $shipping_price]);

        $this->associateTaxesAndDiscountWithGroupOfOrders($coupon,$discount,$order_group);

        $this->updateSubOrdersTaxAndCoupounDiscountAndUsedPoints($order_group,$user,$request->use_points);

        $id = $order->id;
        $image = auth()->user()->image_path;
        $this->sendNotification(
            $id,
            " يوجد طلب جديد من العميل " . auth()->user()->name,
            $image,
            " There is a new order from " . auth()->user()->name
        );
        auth()->user()->notify(new AddNotification('', $id, 'showOrderOnline', "تم انشاء طلبك بنجاح" , $image,"Your order has been created successfully"));

        if($request->payment_method == "Online payment"){
            $my_fatoorah = new MyfatoorahController();
            $response=$my_fatoorah->pay_online_by_myfatoorah($order_group->total_amount,$user,$order_group->id);
            if($response){
                return response()->json(['order' => $order_group->load('orders'), 'url' => $response],200);
            }else{
                return response()->json(['message' => __('text.Not Available Now') ,'response' => $response],404);
            }
        }

        return $this->sendResponse(['order' => $order_group->load('orders'),'url' => ''], trans('message.messageSuccessfully'));
    }


    //validation
    protected function rulesValidation()
    {
        return [
            'payment_method' => ['required',Rule::in(['Cash on delivery','Paypal','Online payment'])],
            'cart' => 'required|array|min:1',
        ];
    }


    //associate order with products
    protected function associateOrderWithProducts($order, $supplier_item)
    {
        $total_amount = 0;
        foreach ($supplier_item as $key => $item) {
            if(is_array($item)){
                $order->products()->attach([$item['price_id'] => [
                    'quantity' => $item['quantity'],
                    'measurement_type' => $item['measurement_type'],
                    'measurement_id' => $item['measurement_id'],
                    'price' => $item['price'] / $item['quantity'], //unit price
                    'sub_total' => $item['price'], //unit price * quantity
                ]]);
                $price = Price::find($item['price_id']);
                $this->recordOrderProductsInDailyOrdersLogs($price->product_id,$item['measurement_id'],$item['quantity']);
                $sold_quantity_in_sub_measurement_unit = $item['measurement_type'] == 'sub' ? $item['quantity'] : $item['quantity'] * $price->product->count_unit;
                $price->update(['quantity' => $price->quantity - $sold_quantity_in_sub_measurement_unit]);
                $total_amount +=$item['price'];
            }
        }
        $order->update(['total_amount' => $total_amount]); //+$order->shipping_cost
    }


    protected function cart_process($cart)
    {
        $cart_data_after_sum_and_group_same_data=collect($cart)->groupBy(['price_id','measurement_id'])->flatMap(function($item){
            return collect($item)->map(function($sub_item){
                return [
                    'price_id' =>$sub_item->first()['price_id'],
                    'measurement_id' =>$sub_item->first()['measurement_id'],
                    'quantity' => $sub_item->sum('quantity')
                ];
            });

        })->values()->all();

        $total_amount = 0;
        $success_cart_items= [];
        $failed_cart_items = [];
        foreach ($cart_data_after_sum_and_group_same_data as $item) {
            $price = Price::find($item['price_id']);
            $product = $price->product;
            $supplier = $price->supplier;
            $stock_quantity = $product->sub_measurement_unit_id == $item['measurement_id'] ? $price->quantity :floor($price->quantity / $product->count_unit);
            $stock_price = $product->sub_measurement_unit_id == $item['measurement_id'] ? ($price->sub_measurement_price_after_sale > 0 ?$price->sub_measurement_price_after_sale: $price->sub_measurement_price ) : ($price->main_measurement_price_after_sale > 0 ?$price->main_measurement_price_after_sale: $price->main_measurement_price);
            if (
                $price
                && $product->status //check if the product is active
                && $stock_quantity >= $item['quantity']
                && $stock_price > 0
                && $supplier->areas()->where('areas.id',$this->current_shop_area_id)->first()  // check if user shop area belongs to supplier areas
              ) {
                $total_amount += $stock_price * $item['quantity'];
                $success_cart_items [] = [
                    "product_id" => $price->product_id,
                    "price_id" => $price->id,
                    "supplier_id" => $price->supplier_id,
                    "price" => $stock_price * $item['quantity'],
                    "quantity" => $item['quantity'],
                    "measurement_id" => $item['measurement_id'],
                    "measurement_type" => $product->sub_measurement_unit_id == $item['measurement_id'] ? 'sub' : 'main',
                ];
            }else{
                $failed_cart_items [] = $item;
            }
        }

        return [
            'success_cart_items' => collect($success_cart_items)->groupBy('supplier_id')->toArray(),
            'failed_cart_items' => $failed_cart_items,
            'total_amount' =>$total_amount
        ];
    }


    protected function days_of_week(){
        return [
            'Sunday',
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
        ];
    }

    protected function associateTaxesAndDiscountWithGroupOfOrders($coupon,$discount,$order_group){
        //calc taxes amount
        $tax_app = new TaxController();
        $taxes = $tax_app->getTaxes();
        $taxes_in_apps = $taxes->getData()->data->taxes;
        $total_tax_amount = 0;
        foreach ($taxes_in_apps as $tax) {
            $total_tax_amount += ($order_group->subtotal * $tax->percentage) / 100;
            $order_group->orderTax()->create([
                'tax_id' => $tax->id,
                'name' => $tax->name,
                'percentage' => $tax->percentage,
            ]);
        }
        $order_group->update([
            'tax_amount' =>  $total_tax_amount,
            'total_amount' => $total_tax_amount + $order_group->total_amount
        ]);


        //coupon
        if($coupon && $discount){
            $coupon->update([
                'used_times' => $coupon->used_times + 1
            ]);
            $order_group->orderDiscount()->create([
                'discount_id' => $coupon->id,
                'code' => $coupon->code,
                'value' => $coupon->value,
                'type' => $coupon->type
            ]);
        }

    }

    protected function updateSubOrdersTaxAndCoupounDiscountAndUsedPoints($order_group,$user,$use_points){

        $used_points=0;
        if($use_points){
            $points = $user->wallet_points;
            $used_points = $points >= $order_group->total_amount ? $order_group->total_amount :$points;
            $diff_in_amount = $order_group->total_amount - $used_points ;
            $order_group->update(['total_amount' => $diff_in_amount , 'used_points' => $used_points]);
            $user->update(['wallet_points' => $user->wallet_points - $used_points]);

        }

            $number_of_sub_orders = $order_group->orders()->count();

            $tax_percentage =$order_group->orderTax->sum('percentage') ;

            $discount_amount_per_sub_order = round($order_group->coupon_discount / $number_of_sub_orders,2);

            foreach($order_group->orders as $order){
                $tax_amount = ($order->subtotal * $tax_percentage) /100 ;
                $total = $order->total_amount - $discount_amount_per_sub_order + $tax_amount;
                $order->update([
                    'coupon' => $order_group->coupon ,
                    'coupon_discount' => $discount_amount_per_sub_order ,
                    'tax_amount' =>$tax_amount ,
                    'total_amount' => $total
                ]);

                if($used_points > 0){
                    $sub_used_points = $used_points >= $order->total_amount ? $order->total_amount :$used_points;
                    $diff_in_amount = $order->total_amount - $sub_used_points ;
                    $order->update(['total_amount' => $diff_in_amount , 'used_points' => floor($sub_used_points)]);
                    $used_points -= $sub_used_points;
                }

                $order->save();
            }

    }

    protected function returnDiscountAndCoupon($request,$total_amount){
          //coupon discount
          if ($request->code) {
            $coupon_controller = new CouponController();
            $coupon_data = $coupon_controller->checkCoupon($request);
            if ($coupon_data->getData()->success == false) {
                return $coupon_data; // return json error
            }

            $coupon = Discount::where('code',$request->code)->first();

            $discount = $coupon->discount($total_amount);
        }

        return ['coupon' => $coupon??null, 'discount' => $discount??0];
    }

    protected function get_cart_proccess_with_delivery_date_for_each_supplier(&$data){
        $cut_off_time = Setting::first()->cut_off_time;
        foreach ($data['success_cart_items'] as $key => $item) {
            $supplier_days = SupplierDay::select('id','day_number')->where('supplier_id',$key)->get()->pluck('day_number')->toArray();
            $today = Carbon::now()->dayOfWeek;
                if(now()->format('H:i:s') >= $cut_off_time)
                    $today = $today == 6 ? 0 : $today+1;
            $tommorow = $today == 6 ? 0 : $today+1;
            $day_of_week__of_delivery_date = "";
            for($i = 0; $i < 7 ; $i++){
                if(!$day_of_week__of_delivery_date && $tommorow != 5) // 5 means  friday
                    $day_of_week__of_delivery_date = in_array($tommorow, $supplier_days) ? $tommorow : '';

                $tommorow = $tommorow == 6 ? 0 : $tommorow+1;
            }

            $data['success_cart_items'][$key]['total_amount']= collect($item)->sum('price');
            $data['success_cart_items'][$key]['delivery_date'] = now()->next($this->days_of_week()[$day_of_week__of_delivery_date])->format('Y-m-d');
        }
    }



    public function sendNotification($id, $message, $image,$message_en)
    {
        User::whereAuthId(1)
            ->whereRelation('roles.notify', 'name', 'Add OnlineOrder')
            ->each(function ($admin) use ($id, $message, $image,$message_en) {
                $admin->notify(new AddNotification('', $id, 'showOrderOnline', $message, $image,$message_en));
            });
    }

    public function trackingOrder()
    {
        $order_group = OrdersGroup::where('user_id',auth()->user()->id)
        ->whereHas('orders',function($q){
            $q->whereIn('order_status' ,['Pending','Processing','Shipping']);
        })->with(['orders'=>function($q) {
            $q->whereIn('order_status' ,['Pending','Processing','Shipping']);
        },'shop','orders.products','orders.products.product'])->latest()->paginate(15);
        $data = array_merge(['data' => OrderGroupResource::collection($order_group->items())],getPaginates($order_group));

        return $this->sendResponse(['order' => $data], trans('message.messageSuccessfully'));

    }

    public function finishedOrders()
    {
        $order_group = OrdersGroup::where('user_id',auth()->user()->id)
        ->whereHas('orders',function($q){
            $q->whereIn('order_status' ,['Canceled','Full Return','Partial Return', 'Completed']);
        })->with(['orders'=>function($q) {
            $q->whereIn('order_status' ,['Canceled','Full Return','Partial Return', 'Completed']);
        },'shop','orders.products','orders.products.product'])->latest()->paginate(15);
        $data = array_merge(['data' => OrderGroupResource::collection($order_group->items())],getPaginates($order_group));

        return $this->sendResponse(['order' => $data], trans('message.messageSuccessfully'));
    }

    public function active_targets(Request $request)
    {
        $user = $request->user();
        $targets = $user->active_targets()->map(function($item) use($user){
            $item->total_amount = $user->completedOrdersbetweenTargetDates($item->start_date,$item->end_date)->get()->sum('total_amount');
            return $item ;
        });

        return $this->sendResponse(['targets' => WalletTargetResource::collection($targets)], trans('message.messageSuccessfully'));
    }

    public function recordOrderProductsInDailyOrdersLogs($product_id,$measurement_id,$qty)
    {
        if($log = DailyOrdersLog::whereDate('date' , now())->where('product_id',$product_id)->where('measurement_id',$measurement_id)->first()){
            $log->update(['quantity' => $log->quantity + $qty]);
        }else{
            DailyOrdersLog::create([
                'date' => now(),
                'product_id' => $product_id,
                'measurement_id' => $measurement_id,
                'quantity' => $qty,
            ]);
        }
    }
}
