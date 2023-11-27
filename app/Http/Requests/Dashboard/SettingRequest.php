<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
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
        return [
            'close' => 'required|boolean',
            'online_payment' => 'required|boolean',
            'best_prices_limits' => 'required',
            'order_amount' => 'required',
            'cut_off_time' => 'required|date_format:H:i',
            'facebook' => 'required|string|min:1',
            'phone' => 'required|regex:/(01)[0-9]{9}/',
            'wats_app' => 'required|regex:/(01)[0-9]{9}/',
        ];

    }
}
