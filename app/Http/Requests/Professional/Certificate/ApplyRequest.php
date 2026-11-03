<?php

namespace App\Http\Requests\Professional\Certificate;

use App\Http\Requests\RestRequest;


class ApplyRequest extends RestRequest
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
            "symbol_number"          => "required|exists:professional,id",
            "professional_id"        => "required|exists:professional,id",
            "level_id"               => "required|exists:levels,id",
            "program_id"             => "required|exists:programs,id",
            "cert_registration_number" => "nullable|string|max:255",
            "voucher_image"          => "nullable",
            "status"                 => "nullable|string|in:completed,pending,refund", // Fixed status rule
            "transaction_id"         => "nullable|string|unique:certificate_renew_requests,transaction_id",
            "txnId"                  => "nullable|string|max:255",
            "exam_id"                => "nullable|string|max:255",
            "tidx"                   => "nullable|string|max:255",
            "amount"                 => "required|numeric|min:0", // Ensure 'amount' is numeric
            "total_amount"           => "nullable|numeric|min:0", // Ensure 'total_amount' is numeric
            "cert_status"            => "nullable|string|in:active,inactive,expired",
            "purchase_order_name"    => "nullable|string|max:255",
            "purchase_order_id"      => "nullable|string|max:255",
            "mobile"                => "nullable|string|max:15 | regex:/^[0-9+\-]+$/", //Optional reger for mobile validation
        ];
    }
}