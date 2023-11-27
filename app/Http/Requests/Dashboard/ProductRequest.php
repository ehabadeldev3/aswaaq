<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'name_ar' => 'required|unique:products,name_ar'. ($this->method() == 'PUT' ? ',' . $this->product->id : ''),
            'name_en' => 'required|unique:products,name_en'. ($this->method() == 'PUT' ? ',' . $this->product->id : ''),
            'count_unit' => 'required|integer',
            'description_ar' => 'required',
            'description_en' => 'required',
            'image' => 'nullable|file|mimes:png,jpg,jpeg,webp',
            'files' => 'nullable|array',
            'files.*' => 'nullable|file|mimes:png,jpg,jpeg,webp',
            'category_id' => 'required|integer|exists:categories,id',
            'company_id' => 'required|integer|exists:companies,id',
            'sub_category_id' => 'required|integer|exists:sub_categories,id',
            'size_id' => 'required|exists:sizes,id',
            'flavor_id' => 'nullable|exists:flavors,id',
            'suppliers' => 'required|array|min:1',
            'suppliers.*' => 'exists:suppliers,id',
            'main_measurement_unit_id' => 'required|exists:measurement_units,id',
            'sub_measurement_unit_id' => 'required|different:main_measurement_unit_id|exists:measurement_units,id',
        ];
        if($this->method() == 'PUT'){
            $rules["image"] = 'nullable' . ($this->hasFile('image') ? '|file|mimes:jpeg,jpg,png,webp' : '');
            $rules["files"] = 'nullable';
            $rules["files.*"] = 'nullable' . ($this->hasFile('files') ? '|file|mimes:jpeg,jpg,png,webp' : '');
        }
        return $rules;
    }
}
