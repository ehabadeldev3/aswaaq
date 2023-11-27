<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class SubCategoryRequest extends FormRequest
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
            'name_ar' => 'required|unique:sub_categories,name_ar' . ($this->method() == 'PUT' ? ',' . $this->subCategory->id : ''),
            'name_en' => 'required|unique:sub_categories,name_en' . ($this->method() == 'PUT' ? ',' . $this->subCategory->id : ''),
            'category_id' => 'required|integer|exists:categories,id',
            'file' => 'required|file|mimes:png,jpg,jpeg',
        ];
        if ($this->method() == 'PUT') {
            $rules["file"] = 'nullable'.($this->hasFile('file')?'|file|mimes:jpeg,jpg,png':'');
        }
        return $rules;
    }
}
