<?php

namespace App\Http\Requests\Admin\Level;
use App\Http\Requests\RestRequest;

class StoreRequest extends RestRequest
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
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'nullable|integer',
            'name' => 'required|string',
            'short_name_english' => 'required',
            'short_name_nepali' => 'nullable',
            'code' => 'nullable',
        ];
    }
}
