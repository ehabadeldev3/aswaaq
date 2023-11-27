<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class DiscountRequest extends FormRequest
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
            'code'              => 'required|unique:discounts,code'. ($this->method() == 'PUT' ? ',' . $this->discount->id : ''),
            'type'              => 'required',
            'value'             => 'required|numeric',
            'description'       => 'nullable',
            'use_times'         => 'required|numeric',
            'start_date'        => 'nullable|date_format:Y-m-d',
            'expire_date'       => 'required_with:start_date|date_format:Y-m-d',
            'greater_than'      => 'nullable|numeric',
        ];

    }
}
