<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest
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
            'name_ar' => 'required|unique:companies,name_ar'. ($this->method() == 'PUT' ? ',' . $this->company->id : ''),
            'name_en' => 'required|unique:companies,name_en'. ($this->method() == 'PUT' ? ',' . $this->company->id : ''),
            'file' => 'required|file|mimes:png,jpg,jpeg',
        ];
        if($this->method() == 'PUT'){
            $rules["file"] = 'nullable'.($this->hasFile('file')?'|file|mimes:jpeg,jpg,png':'');
        }
        return $rules;
    }
}
