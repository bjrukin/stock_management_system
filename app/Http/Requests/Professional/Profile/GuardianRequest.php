<?php

namespace App\Http\Requests\Professional\Profile;
use App\Http\Requests\RestRequest;

class GuardianRequest extends RestRequest
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
            'father_name' => 'required',
            'father_name_nep' => 'required',
            'mother_name' => 'required',
            'mother_name_nep' => 'required',
            'grandfather_name' => 'required',
            'grandfather_name_nep' => 'required',
        ];
    }
}
