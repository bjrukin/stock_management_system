<?php

namespace App\Http\Requests\Admin\Professional;

use App\Http\Requests\RestRequest;

class PclRequest extends RestRequest
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
            'admission_year' => 'required|integer',
            'passed_year' => 'required|integer',
            'board_university' => 'required',
            'registration_number' => 'required',
            'transcript_image' => 'required',
            'transcript_bac_1' => 'sometimes',
            'transcript_bac_2' => 'sometimes',
            'provisional_image' => 'required',
            'character_image' => 'required',
            'equivalence_certificate' => 'sometimes',
            'council_registration_certificate' => 'sometimes',
            'ojt_pcl_community_1_image' => 'sometimes',
            'ojt_pcl_community_2_image' => 'sometimes',
            'noc_image' => 'sometimes',
        ];
    }

    public function messages()
    {
        return [
            'international_college_name.required' => 'College name is required'
        ];
    }
}
