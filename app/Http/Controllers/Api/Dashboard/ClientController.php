<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\SendNotificationToCLientsRequest;
use App\Http\Resources\UserResource;
use App\Models\Client;
use App\Models\OrdersGroup;
use App\Models\Province;
use App\Models\User;
use App\Traits\Message;
use App\Traits\NotificationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{

    use Message;
    use NotificationTrait;

    public function __construct()
    {
        $this->middleware('permission:client read', ['only' => ['index','get_client_doesnt_have_orders','client_details','client_orders']]);
        $this->middleware('permission:client edit', ['only' => ['activationClient', 'edit', 'sendNotificationToAllClients' , 'sendNotificationToClient']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function activationClient(User $user)
    {
        if($user->status == 0){
            $body = "تم تفعيل حسابك بنجاح";
            $type = "active client";
            $this->notification([$user->client->firebase_token],$body,$type,$user->image_path);
        }
        $user->update([
            "status" => $user->status == 1 ? 0 : 1
        ]);

        return $this->sendResponse([], 'Data exited successfully');
    }

    public function index(Request $request)
    {
        $clients_query = User::with('client.shops')->whereHas('client')
        ->withSum(['orders as total_amount' => function ($query) {
                $query->whereIn('order_status' , ['Completed','Partial Return'])->where('confirmed_received_amount',1);
        }], 'subtotal')

        ->where(function ($q) use ($request) {
            $q->when($request->search, function ($q) use ($request) {
                $q->where("name", "like", "%$request->search%")
                    ->orWhere("phone", "like", "%$request->search%")
                    ->orWhere("email", "like", "%$request->search%");
            });
        })
        ->where(function ($q) use ($request) {
            $q->when($request->from_date && $request->to_date, function ($q) use ($request) {
                $q->whereDate("created_at",'>=', $request->from_date)
                ->whereDate("created_at",'<=', $request->to_date);
            });
        });
        if($request->product_filter){
            $clients_query->orderBy('total_amount',strpos($request->product_filter,'least') === 0 ? 'asc' :'desc');
        }else{
            $clients_query->latest();
        }
         $clients= $clients_query->paginate($request->pagination ?? 25);

        return response()->json(['clients' => $clients]);

    }


    public function get_client_doesnt_have_orders(Request $request)
    {
        $clients = User::whereDoesntHave('orders')->whereHas('client')
            ->where(function ($q) use ($request) {
                $q->when($request->search, function ($q) use ($request) {
                    $q->where("name", "like", "%$request->search%")
                        ->orWhere("phone", "like", "%$request->search%")
                        ->orWhere("email", "like", "%$request->search%");
                });
            })
            ->latest()->paginate(10);

        return response()->json(['clients' => $clients]);
    }



    public function client_details(User $user)
    {
        $user->order_numbers = OrdersGroup::where('user_id', $user->id)->count();
        return response()->json(['client' => $user]);
    }

    public function client_orders(Request $request)
    {
        $orders = OrdersGroup::with(['orders','orders.products','orders.products.product.flavor','orders.products.product.size','orders.supplier','orders.user','orders.shop','orders.notes'])->where('user_id', $request->user_id)->latest()->paginate(10);
        return response()->json(['orders' => $orders]);
    }



    public function sendNotificationToAllClients(SendNotificationToCLientsRequest $request)
    {
        //    $product = Product::find($request->product_id);
        $tokens = Client::whereNotNull('firebase_token')->get('firebase_token')->pluck('firebase_token');
        $title = $request->title;
        $body = $request->notification;
        $image = "";
        if($request->hasFile('image')){
            $image = time().'.'. $request->image->getClientOriginalName();
            // picture move
            $request->image->storeAs('category', $image,'general');
            $image = asset('upload/category/'.$image);
        }
        $response =   $this->notification($tokens, $body, $title, "send notification",$image);

        return response()->json($response, 200);
    }

    public function sendNotificationToClient(SendNotificationToCLientsRequest $request)
    {
        // Validator request
        $request->validate(['user_id' => 'required|exists:users,id']);

        $client = Client::where('user_id',$request->user_id)->first();

        if ($client &&  $client->firebase_token) {
            $image = "";
            if($request->hasFile('image')){
                $image = time().'.'. $request->image->getClientOriginalName();
                // picture move
                $request->image->storeAs('category', $image,'general');
                $image = asset('upload/category/'.$image);
            }
            $title = $request->title;
            $body = $request->notification;
            $type = "send notification";
            $response = $this->notification([$client->firebase_token], $body, $title, $type,$image);
            return response()->json($response, 200);
        }
    }

}
