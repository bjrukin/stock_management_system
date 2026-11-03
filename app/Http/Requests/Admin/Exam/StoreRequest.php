<?php

namespace App\Http\Requests\Admin\Exam;
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
            'id'                  => 'nullable|integer',
            'exam_number'         => 'required|integer',
            'name'                => 'required|string',
            'name_nep'            => 'nullable',
            'opening_date'        => 'required|date',
            'closing_date'        => 'required|date',
            'exam_fee'            => 'required',
            'late_fee_start_date' => 'required|date',
            'late_fee'            => 'required',
            'description'         => 'nullable',
        ];
    }
}
