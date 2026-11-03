<?php

namespace App\Http\Requests\Admin\Professional;

use App\Http\Requests\RestRequest;

class BachelorRequest extends RestRequest
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
            'id' => 'required',
            'college_name' => 'required',
            'passed_year' => 'required|integer',
            'admission_year' => 'required|integer',
            'board_university' => 'required',
            'registration_number' => 'required',
            'transcript_bac_1' => 'sometimes',
            'transcript_bac_2' => 'sometimes',
            'transcript_bac_3' => 'sometimes',
            'transcript_bac_4' => 'sometimes',
            'transcript_bac_5' => 'sometimes',
            'transcript_bac_6' => 'sometimes',
            'transcript_bac_7' => 'sometimes',
            'transcript_bac_8' => 'sometimes',
            'equivalence_certificate' => 'sometimes',
            'provisional_image' => 'required',
            'character_image' => 'required',
            'intership_image' => 'sometimes',
            'noc_image' => 'sometimes',
            'visa_image' => 'sometimes',
            'passport_image' => 'sometimes',
        ];
    }
}
