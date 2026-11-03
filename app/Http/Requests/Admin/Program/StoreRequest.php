<?php

namespace App\Http\Requests\Admin\Program;
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
            'code' => 'required',
            'name' => 'required|string',
            'certificate_name' => 'required',
            'qualification' => 'required',
            'level_id' => 'required',
            'subject_committee_id' => 'required',
            'program_duration' => 'required|integer',
            'duration_type' => 'required',
            'program_type' => 'required',
            'has_exam' => 'required',
        ];
    }
}
