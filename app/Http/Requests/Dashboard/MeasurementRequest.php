<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class MeasurementRequest extends FormRequest
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
            'name_ar' => 'required|unique:measurement_units,name_ar'. ($this->method() == 'PUT' ? ',' . $this->id : ''),
            'name_en' => 'required|unique:measurement_units,name_en'. ($this->method() == 'PUT' ? ',' . $this->id : ''),
        ];

    }
}
