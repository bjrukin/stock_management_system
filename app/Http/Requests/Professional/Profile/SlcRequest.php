<?php

namespace App\Http\Requests\Professional\Profile;

use App\Http\Requests\RestRequest;

class SlcRequest extends RestRequest
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
            'id' => 'nullable',
            'level_id' => 'required',
            'school_name' =>  'required',
            'board_university' => 'required',
            'passed_year' => 'required|integer',
            'result_type' => 'required|in:gpa,percentage',
            'gpa' => 'required_if:result_type,gpa|nullable|numeric|min:0|max:4',
            'percentage' => 'required_if:result_type,percentage|nullable|numeric|min:0|max:100',
            'transcript_image' => 'required',
            'transcript_bac_1' => 'sometimes',
            'provisional_image' => 'required',
            'character_image' => 'required',
            'equivalence_certificate' => 'sometimes',
        ];
    }
}
