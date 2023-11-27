<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
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
            'bank_name' => 'required',
            'bank_address' => 'required',
            'bank_iban' => 'required',
            'bank_swift' => 'present',
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email'.($this->method() == 'PUT' ? ',' . $this->employee->user_id : '') ,
            'password' => 'required|string|min:8',
            'confirmtion' => 'required|same:password',
            'department_id' => 'required|integer|exists:departments,id',
            'job_id' => 'required|integer|exists:jobs,id',
            'phone' => 'required|string|unique:users,phone'. ($this->method() == 'PUT' ? ',' . $this->employee->user_id : ''),
            'role_name' => 'required',
            'address' => 'required|string|min:3',
            'National_ID' => 'required|string|min:8|unique:employees,National_ID'. ($this->method() == 'PUT' ? ',' . $this->employee->id : ''),
            'birth_date' => 'required|date',
            'hiring_date' => 'required|date',
            'salary' => 'required',
            'file' => 'required|image|mimes:jpeg,jpg,png,webp|max:5048',
        ];

        if($this->method() == 'PUT'){
            $rules["file"] = 'nullable'.($this->hasFile('file')?'|file|mimes:jpeg,jpg,png,webp|max:5048':'');
            $rules['password'] = 'nullable';
            $rules['confirmtion'] = 'nullable';
        }
        return $rules;
    }
}
