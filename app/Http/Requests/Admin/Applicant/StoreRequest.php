<?php

namespace App\Http\Requests\Admin\Applicant;
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
            "id" => "required",
            "exam_id" => "nullable",
            "level" => "required",
            "program" => "required",
            "voucher_image" => "required",
        ];
    }
}
