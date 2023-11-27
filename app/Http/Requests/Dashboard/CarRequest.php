<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class CarRequest extends FormRequest
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
        $rules = [
            'name_ar' => 'required|string',
            'name_en' => 'required|string',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'plate_number' => 'required|string',
            'phone' => 'nullable|numeric',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp'
        ];

        if($this->method() == 'PUT'){
            $rules["license"] = 'nullable'.($this->hasFile('file')?'|file|mimes:jpeg,jpg,png,webp':'');
        }
        return $rules;
    }
}
