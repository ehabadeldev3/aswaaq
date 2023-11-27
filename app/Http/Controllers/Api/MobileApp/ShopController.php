<?php

namespace App\Http\Controllers\Api\MobileApp;

use App\Http\Controllers\Controller;
use App\Http\Resources\MobileResources\ShopResource;
use App\Models\Shop;
use App\Models\User;
use App\Notifications\Admin\AddNotification;
use App\Traits\Message;
use Illuminate\Http\Request;
class ShopController extends Controller
{
    use Message;


    public function index(Request $request)
    {
        $user_shops = auth()->user()->client->shops()->with(['media','province','area'])->get();
        return $this->sendResponse(['shops' => ShopResource::collection($user_shops),'image_path' => asset('upload/user/')], trans('message.messageSuccessfully'));
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required|string|min:3|max:190',
            'phone' => 'required|string|min:11|unique:shops,phone',
            'address' => 'required|string|min:3|max:255',
            'feature_sign' => 'nullable|',
            'land_line' => 'nullable|',
            'geolocation' => 'required|string|min:3|max:255',
            'images' => 'required|array|min:1',
            'images.*' => 'mimes:jpeg,jpg,png,gif',
            'province_id' => 'required|integer|exists:provinces,id',
            'area_id' => 'required|integer|exists:areas,id',
        ]);
        $client = auth()->user()->client;
        $request_data = $request->only(['name','geolocation','phone','address','land_line','feature_sign','province_id','area_id']);
        $request_data['client_id'] = $client->id;

        $shop = Shop::create($request_data);

        //make first shop the default
        if(!$client->selected_shop_id)
            $client->update(['selected_shop_id' => $shop->id]);


        //store image
        foreach($request->images as $image_file)
            $this->storeImages($image_file,$shop);

        $client_name = auth()->user()->name;
        $message = ` تم تسجيل محل جديد باسم $shop->name للعميل $client_name `;
        $message_en = `New Shop has been registered called $shop->name by $client_name`;
        User::whereAuthId(1)
            ->whereRelation('roles.notify','name','Add Shop')
            ->each(function ($admin) use($message,$message_en){
                $admin->notify(new AddNotification('',auth()->user()->id,'client_profile',$message,'',$message_en));
            });
        auth()->user()->notify(new AddNotification('',auth()->user()->id,'client_profile','تم تسجيل محل جديد','','New Shop has been registered'));
        return $this->sendResponse([], 'Data exited successfully');
    }

    public function update(Request $request,$id){
        $shop = Shop::findOrFail($id);
        $request->validate([
            'name' => 'required|string|min:3|max:190',
            'phone' => 'required|string|min:11|unique:shops,phone,'. $shop->id,
            'address' => 'required|string|min:3|max:255',
            'feature_sign' => 'nullable',
            'land_line' => 'nullable',
            'geolocation' => 'required|string|min:3|max:255',
            'province_id' => 'required|integer|exists:provinces,id',
            'area_id' => 'required|integer|exists:areas,id',
        ]);

        $request_data = $request->only(['name','geolocation','phone','address','land_line','feature_sign','province_id','area_id']);

        $shop->update($request_data);
        //store image

        // foreach($request->images as $image_file)
        //     $this->storeImages($image_file,$shop);


        return $this->sendResponse([], 'Data exited successfully');
    }


    public function destroy($id)
    {
        auth()->user()->client->shops()->findOrFail($id)->delete();
        return $this->sendResponse([], 'Deleted successfully');
    }

    public function update_selected_shop($id)
    {
        $client = auth()->user()->client;
        if($client->shops->where('id',$id)){
            $client->update(['selected_shop_id' => $id]);
            return $this->sendResponse([], 'Deleted successfully');
        }else{
            return $this->sendError([], 'Not found');
        }
    }


    protected function storeImages($image_file,$model)
    {
        saveFile($image_file,$model,'user');
    }

}
