<?php

namespace App\Http\Requests\Admin\Applicant;
use App\Http\Requests\RestRequest;

class ExportRequest extends RestRequest
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
            "exam_id" => "required",
            "level_id" => "nullable",
            "program_id" => "nullable",
            "state" => "nullable",
            "status" => "nullable",
        ];
    }
}
