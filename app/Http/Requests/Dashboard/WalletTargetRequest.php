<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class WalletTargetRequest extends FormRequest
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
            'amount' => 'required|integer|min:1',
            'selected_client_category' => 'required|integer',
            'start_date' => 'required|date|after:yesterday',
            'end_date' => 'required|date|after:start_date',
            'clients' => 'nullable',
            'clients.*' => 'exists:users,id',
            'points' => 'required|integer|min:1',
        ];

        if($this->selected_client_category > 0){
            $rules['clients'] = 'required|array|min:1';
        }

        return $rules;
    }
}
