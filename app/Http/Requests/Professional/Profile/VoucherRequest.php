<?php

namespace App\Http\Requests\Professional\Profile;

use App\Http\Requests\RestRequest;

class VoucherRequest extends RestRequest
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
            'exam_apply_id' => 'required',
            'voucher_image' => 'required',
        ];
    }
}
