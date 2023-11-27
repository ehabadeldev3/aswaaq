<?php

namespace App\Http\Controllers\Api\MobileApp;

use App\Http\Controllers\Controller;
use App\Http\Resources\MobileResources\SliderAdsResource;
use App\Models\Ad;
use App\Models\PopupAds;
use App\Traits\Message;
use Illuminate\Http\Request;

class AdsController extends Controller
{
    use Message;

    public $current_shop_area_id;
    public function __construct( )
    {
        $this->current_shop_area_id = current_shop_area_id();
    }

    public function slidersAds(){

        $top = Ad::where('place',1)->with(['product' => function ($q){
            $q->productsHasSuppliersHasPricesByAreaId($this->current_shop_area_id);
        },'sub_category'])->get();

        $bottom = Ad::where('place',2)->with(['product' => function ($q){
            $q->productsHasSuppliersHasPricesByAreaId($this->current_shop_area_id);
        },'sub_category'])->get();

        return $this->sendResponse(['top' => SliderAdsResource::collection($top),'bottom'=>SliderAdsResource::collection($bottom)], trans('message.messageSuccessfully'));
    }
}
