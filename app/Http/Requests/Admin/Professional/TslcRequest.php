<?php

namespace App\Http\Requests\Admin\Professional;

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
            'id' => 'required',
            'college_name' => 'required',
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
