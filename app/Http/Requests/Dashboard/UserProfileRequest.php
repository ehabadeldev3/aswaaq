<?php

namespace App\Http\Requests\Dashboard\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class UserProfileRequest extends FormRequest
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
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email,' . $this->user,
            'phone' => 'required|string|unique:users,phone,' . $this->user,
            'password' => 'nullable|string|min:8',
            'confirmtion' => 'nullable|same:password',
        ];
    }
}
