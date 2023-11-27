<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class DepartmentRequest extends FormRequest
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
            'name_ar' => 'required|unique:departments,name_ar'. ($this->method() == 'PUT' ? ',' . $this->department->id : ''),
            'name_en' => 'required|unique:departments,name_en'. ($this->method() == 'PUT' ? ',' . $this->department->id : ''),
        ];

    }
}
