<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class LoadingManRequest extends FormRequest
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
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email'. ($this->method() == 'PUT' ? ',' . $this->loading_man->user_id : ''),
            'password' => 'required|string|min:8',
            'confirmtion' => 'required|same:password',
            'phone' => 'required|string|unique:users,phone'. ($this->method() == 'PUT' ? ',' . $this->loading_man->user_id : ''),
            'address' => 'required|string|min:3',
            'National_ID' => 'required|string|min:8|unique:loading_men,National_ID'. ($this->method() == 'PUT' ? ',' . $this->loading_man->id : ''),
            'birth_date' => 'required|date',
            'hiring_date' => 'required|date',
            'salary' => 'required',
            'file' => 'image|mimes:jpeg,jpg,png,webp|max:5048',
        ];

        if($this->method() == 'PUT'){
            $rules['password'] = 'nullable';
            $rules['confirmtion'] = 'nullable';
            $rules["file"] = 'nullable'.($this->hasFile('file')?'|file|mimes:jpeg,jpg,png,webp|max:5048':'');
        }
        return $rules;
    }
}
