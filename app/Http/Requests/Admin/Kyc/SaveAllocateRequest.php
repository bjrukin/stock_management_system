<?php

namespace App\Http\Requests\Admin\Kyc;
use App\Http\Requests\RestRequest;

class SaveAllocateRequest extends RestRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     *
     * @return array
     */
    public function rules() {
        return [
            'id'              => 'required|integer',
            'professional_id' => 'required|integer',
        ];
    }
}