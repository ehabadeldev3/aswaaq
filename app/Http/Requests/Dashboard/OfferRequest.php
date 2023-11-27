<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class OfferRequest extends FormRequest
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
            "description_ar" => "required",
            "description_en" => "required",
            "image" => "required|image",
        ];
        if($this->method() == 'PUT'){
            $rules["id"] = "required";
            $rules["image"] = "nullable|image";
        }
        if ($this->external=="true") {
            $rules["url"] = "required";
        } else {
            $rules["product_id"] = "required";
        }
        return $rules;
    }
}
