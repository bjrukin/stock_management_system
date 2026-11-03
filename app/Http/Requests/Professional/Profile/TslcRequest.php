<?php

namespace App\Http\Requests\Professional\Profile;

use App\Http\Requests\RestRequest;

class TslcRequest extends RestRequest
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
            'passed_year' => 'required|integer',
            'board_university' => 'required',
            'registration_number' => 'required',
            'transcript_image' => 'required',
            'transcript_bac_1' => 'sometimes',
            'transcript_bac_2' => 'sometimes',
            'transcript_bac_3' => 'sometimes',
            'provisional_image' => 'required',
            'character_image' => 'required',
            'ojt_image' => 'required',
        ];
    }
}
