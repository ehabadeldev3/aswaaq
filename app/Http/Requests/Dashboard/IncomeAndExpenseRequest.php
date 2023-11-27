<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class IncomeAndExpenseRequest extends FormRequest
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
            'payment_date' => 'required|date',
            'income_id' => 'nullable|exists:incomes,id',
            'expense_id' => 'nullable|exists:expenses,id',
            'notes' => 'nullable|string|min:3',
            'payer' => 'nullable|string|min:3',
            'treasury_id' => 'required|exists:treasuries,id',
        ];

    }
}
