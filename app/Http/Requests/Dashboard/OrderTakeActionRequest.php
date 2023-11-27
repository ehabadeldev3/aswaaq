<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class OrderTakeActionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return[
            'orders' => 'required|array|min:1',
            'orders.*' => 'exists:orders,id',
            'representative_id' => 'nullable|exists:users,id',
            'car_id' => 'nullable|exists:cars,id',
            'order_status' => 'nullable|in:Pending,Processing,Shipping,Completed,Canceled,Full Return,Partial Return,Hold'
        ];
    }
}
