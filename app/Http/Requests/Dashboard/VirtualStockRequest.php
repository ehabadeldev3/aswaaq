<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class VirtualStockRequest extends FormRequest
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
            'product.*.supplier_id'     => 'required|exists:suppliers,id',
            'product.*.product_id'      => 'required|exists:products,id',
            'product.*.category_id'      => 'required|exists:categories,id',
            'product.*.sub_category_id'      => 'required|exists:sub_categories,id',
            'product.*.quantity'        => 'required|integer|min:1',
            'product.*.main_measurement_price'        => 'required|integer|min:1',
            'product.*.sub_measurement_price'        => 'nullable',
            'product.*.main_measurement_price_after_sale'        => 'nullable|lt:product.*.main_measurement_price',
            'product.*.sub_measurement_price_after_sale'        => 'nullable|lte:product.*.sub_measurement_price',
        ];
    }
}
