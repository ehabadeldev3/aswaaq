<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class ShiftRequest extends FormRequest
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
            'name_ar' => 'required|unique:shifts,name_ar'. ($this->method() == 'PUT' ? ',' . $this->shift->id : ''),
            'name_en' => 'required|unique:shifts,name_en'. ($this->method() == 'PUT' ? ',' . $this->shift->id : ''),
            'type' => 'required',
            'start' => 'required',
            'end' => 'required|after:start',
        ];

    }
}
