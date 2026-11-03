<?php

namespace App\Http\Requests\Professional\Kyc;
use App\Http\Requests\RestRequest;

class KycRequest extends RestRequest
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
            "name" => "required",
            "dob" => "required",
            "profile_img" => "required",
            "symbol_number" => "sometimes",
            "registration_number" => "sometimes",
        ];
    }
}
