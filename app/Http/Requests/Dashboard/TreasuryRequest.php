<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class TreasuryRequest extends FormRequest
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
            'name_ar' => 'required|unique:treasuries,name_ar' . ($this->method() == 'PUT' ? ',' . $this->treasury->id : ''),
            'name_en' => 'required|unique:treasuries,name_en' .($this->method() == 'PUT' ? ',' . $this->treasury->id : ''),
            'treasury_id' => 'nullable|integer|exists:treasuries,id',
            'amount' => 'required'
        ];

    }
}
