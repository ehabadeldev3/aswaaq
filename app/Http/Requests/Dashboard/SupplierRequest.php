<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierRequest extends FormRequest
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
        $is_our_supplier_rule = Rule::unique('suppliers')->where(fn ($query) => $query
        ->where('is_our_supplier', 1));
        return [
            "name" => "required",
            "address" => "required",
            "commission_percentage" => "nullable",
            "responsible_name" => "nullable",
            "responsible_phone" => "nullable",
            "areas" => "required|array|min:1",
            "areas.*" => "exists:areas,id",
            "phone" => "required|regex:/^01[0125][0-9]{8}$/|unique:suppliers,phone" . ($this->method() == 'PUT' ? ',' . $this->supplier : ''),
            "commerical_register" => "nullable|unique:suppliers,commerical_register" .($this->method() == 'PUT' ? ',' . $this->supplier : ''),
            "tax_card" => "nullable|unique:suppliers,tax_card" .($this->method() == 'PUT' ? ',' . $this->supplier : ''),
            "is_our_supplier" => ["required", "boolean",$this->method() == 'PUT' ? $is_our_supplier_rule->ignore($this->supplier): $is_our_supplier_rule],
            'days' => ['required','array','min:1'],
            'days.*' => "in:0,1,2,3,4,5,6"
        ];
    }
}
