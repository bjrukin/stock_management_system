<?php

namespace App\Http\Requests\Admin\EligibilityMark;
use App\Http\Requests\RestRequest;

class StoreRequest extends RestRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     *
     * @return array
     */
    public function rules() {
        return [
            'id'                    => 'integer|nullable',
            'level_id'              => 'required',
            'min_percentage'        => 'nullable',
            'min_gpa'               => 'nullable',
            'min_physics_marks'     => 'required',
            'min_chemistry_marks'   => 'required',
            'min_bio_marks'         => 'required',
            'total_days_passport'   => 'required|integer',
            'total_days_internship' => 'required|integer',
        ];
    }
}