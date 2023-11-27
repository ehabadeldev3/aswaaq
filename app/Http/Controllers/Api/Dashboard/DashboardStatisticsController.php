<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAreaRequest;
use App\Http\Requests\UpdateAreaRequest;
use App\Models\Client;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Order;
use App\Models\Price;
use App\Models\Product;
use App\Models\Representative;
use App\Models\Supplier;
use App\Repositories\AreaRepository;
use Carbon\Carbon;

class DashboardStatisticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:dashboard statistics read', ['only' => ['index']]);
    } 
    public function index()
    {
        $orders = Order::whereIn('order_status',['Completed','Partial Return'])->where('confirmed_received_amount',1)->get();
        $number_of_clients = Client::count();
        $orders_count = $orders->count();
        $orders_total_amount = $orders->sum('total_amount');
        $number_of_representatives = Representative::count();
        $number_of_suppliers = Supplier::count();
        $number_of_companies = Company::whereStatus(1)->count();
        $number_of_employess = Employee::count();
        $number_of_products = Product::whereStatus(1)->count();
        $date = $this->getCurrentMonthAndLastMonthDate();
        $orders_for_current_month =  Order::getCompletedOrdersThroughMonthGroubByWeeks($date['current_year'],$date['current_month']);
        $orders_for_last_month =  Order::getCompletedOrdersThroughMonthGroubByWeeks($date['last_year'],$date['last_month']);

        return response()->json([
            'number_of_clients' =>$number_of_clients ,
            "orders_count" => $orders_count ,
            'orders_total_amount' => $orders_total_amount ,
            'number_of_products' => $number_of_products,
            'orders_for_current_month' => $orders_for_current_month ,
            'number_of_representatives' => $number_of_representatives ,
            'number_of_employess' => $number_of_employess ,
            'number_of_companies' => $number_of_companies ,
            'number_of_suppliers' => $number_of_suppliers ,
            'orders_for_last_month' => $orders_for_last_month
        ]);
    }


    protected function getCurrentMonthAndLastMonthDate(){
        $date['current_month']= Carbon::now()->month;
        $date['current_year']= Carbon::now()->year;
        if($date['current_month'] == 1){
            $date['last_month']=12;
            $date['last_year']=$date['current_year']-1;
        }else{
            $date['last_month']=$date['current_month']-1;
            $date['last_year']=$date['current_year'];
        }

        return $date;
    }

}
