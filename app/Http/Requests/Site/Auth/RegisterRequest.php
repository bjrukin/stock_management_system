<?php

namespace App\Http\Requests\Site\Auth;

use App\Http\Requests\RestRequest;

class RegisterRequest extends RestRequest
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
            'name' => 'required',
            'email' => 'required|email:filter|unique:professionals,email',
            'phone' =>  'required|regex:/^([9]{1})([7-8]{1})([0-9]{8})$/|min:10|max:10',
            'password' => 'required|min:8',
            'is_foreign' => 'nullable',
        ];
    }
}
