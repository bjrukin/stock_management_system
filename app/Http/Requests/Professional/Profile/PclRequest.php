<?php

namespace App\Http\Requests\Professional\Profile;

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
            'id' => 'nullable',
            'level_id' => 'required',
            'college_type' => 'nullable',
            'college_name' => (isset($this->college_type) && $this->college_type == 'nepal') ? 'required' : 'nullable',
            'international_college_name' => ((isset($this->college_type) && $this->college_type == 'nepal') || ($this->id > 0)) ? 'nullable' : 'required',
            'admission_year' => 'required|integer',
            'passed_year' => 'required|integer',
            'board_university' => 'required',
            'registration_number' => 'required',
            'result_type' => 'required|in:gpa,percentage',
            'percentage' => 'required_if:result_type,percentage|nullable|numeric|min:0|max:100',
            'gpa' => 'required_if:result_type,gpa|nullable|numeric|min:0|max:4',
            'physics_marks' => 'nullable|numeric|min:0|max:100',
            'chemistry_marks' => 'nullable|numeric|min:0|max:100',
            'biology_marks' => 'nullable|numeric|min:0|max:100',
            'average' => 'nullable|numeric|min:0|max:100',
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
