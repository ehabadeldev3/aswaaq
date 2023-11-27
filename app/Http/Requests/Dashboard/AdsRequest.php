<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class AdsRequest extends FormRequest
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
            'product_id' => 'nullable|'.$this->type == 'product' ? 'exists:products,id' : '',
            'sub_category_id' => 'nullable|'.$this->type == 'sub_category' ? 'exists:sub_categories,id' : '',
            'file' => 'required|mimes:jpeg,jpg,png,webp|max:5048',
            'place' => 'required|integer',
            'type' => 'required|in:product,sub_category',
        ];
        if($this->method() == 'PUT'){
            $rules["file"] = 'nullable' . ($this->hasFile('file') ? '|file|mimes:jpeg,jpg,png|max:5048' : '');
        }
        return $rules;
    }
}
