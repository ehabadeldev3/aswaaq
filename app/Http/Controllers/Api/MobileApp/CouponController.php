<?php

namespace App\Http\Controllers\Api\MobileApp;

use App\Http\Controllers\Controller;
use App\Http\Resources\MobileResources\CouponResource;
use App\Models\Discount;
use App\Traits\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CouponController extends Controller
{
    use Message;

    public function checkCoupon(Request $request){

        // Validator request
        $request->validate([
            'code' => 'required|exists:discounts,code',
        ]);

        $coupon = Discount::where('code',$request->code)->first();

        if ($coupon->UserUsedItBefore()){
            return $this->sendError(trans("message.This coupon has been used before") );
        }

        if ($coupon->start_date > now()->format('Y-m-d')){
            return $this->sendError(trans("message.This coupon has not started yet") );
        }

        if ($coupon->expire_date < now()->format('Y-m-d') || $coupon->status == 0 || $coupon->use_times == $coupon->used_times){
            return $this->sendError(trans('message.This coupon has expired') );
        }

        return $this->sendResponse(['coupon' => new CouponResource($coupon)], trans('message.messageSuccessfully'));

    }

}
