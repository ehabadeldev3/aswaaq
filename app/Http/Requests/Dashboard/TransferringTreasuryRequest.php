<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class TransferringTreasuryRequest extends FormRequest
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
            'from_treasury_id' => 'required|integer|exists:treasuries,id',
            'to_treasury_id' => 'required|integer|exists:treasuries,id',
            'amount' => 'required',
            'notes' => 'required',
        ];

    }
}
