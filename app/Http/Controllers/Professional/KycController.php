<?php

namespace App\Http\Controllers\Professional;

use App\Http\Requests\Professional\Kyc\KycRequest;
use App\Http\Controllers\Controller;
use App\Models\Kyc;
use App\Services\Professional\KycService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KycController extends Controller
{
    public function __construct(protected KycService $kycService)
    {
    }
    public function index()
    {
        $data['kyc'] = Kyc::where('professional_id', Auth::guard('professional')->id())->first();
        $data['professional'] = Auth::guard('professional')->user()->load('info');
        $data['nav'] = 'kyc';
        $data['sub_nav'] = '';
        $data['page_title'] = "KYC Form";
        return view('professional.kyc.index', $data);
    }

    public function saveKyc(KycRequest $request)
    {
        return $this->kycService->saveApply($request->validated());
    }
}
