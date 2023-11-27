<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
            'name_ar' => 'required|unique:categories,name_ar'. ($this->method() == 'PUT' ? ',' . $this->category->id : ''),
            'name_en' => 'required|unique:categories,name_en'. ($this->method() == 'PUT' ? ',' . $this->category->id : ''),
            'file' => 'required|file|mimes:png,jpg,jpeg',
        ];
        if($this->method() == 'PUT'){
            $rules["file"] = 'nullable'.($this->hasFile('file')?'|file|mimes:jpeg,jpg,png':'');
        }
        return $rules;
    }
}
