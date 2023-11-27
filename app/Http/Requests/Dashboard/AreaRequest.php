<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class AreaRequest extends FormRequest
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
            'name_ar' => 'required|unique:areas,name_ar'. ($this->method() == 'PUT' ? ',' . $this->area->id : ''),
            'name_en' => 'required|unique:areas,name_en'. ($this->method() == 'PUT' ? ',' . $this->area->id : ''),
            'province_id' => 'required|integer|exists:provinces,id',
            'shipping_price' => 'required|integer',
        ];

    }
}
