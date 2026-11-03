<?php

namespace App\Http\Requests\Admin\Permission;
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
            'role' => 'required|integer',
            'module.*' => 'required|integer',
            'list.*' => 'nullable',
            'create.*' => 'nullable',
            'edit.*' => 'nullable',
            'delete.*' => 'nullable',
        ];
    }
}
