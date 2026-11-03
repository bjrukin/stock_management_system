<?php

namespace App\Http\Requests\Admin\Professional;

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
            'id' => 'required',
            'school_name' =>  'required',
            'board_university' => 'required',
            'passed_year' => 'required|integer',
            'transcript_image' => 'required',
            'transcript_bac_1' => 'sometimes',
            'provisional_image' => 'required',
            'character_image' => 'required',
            'equivalence_certificate' => 'sometimes',
        ];
    }
}
