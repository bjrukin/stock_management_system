<?php

namespace App\Http\Requests\Professional\Exam;
use App\Http\Requests\RestRequest;

class ApplicationRequest extends RestRequest
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
            "exam_id" => "sometimes",
            "level" => "required",
            "program" => "required",
            "payment_by" => "required|in:Bank,Khalti",
            "voucher_image" => "required_if:payment_by,voucher",
            // 'biometric_photo' => $this->level == 4 ? 'nullable' : 'required',
            'biometric_photo' => 'nullable',
        ];
    }
}
