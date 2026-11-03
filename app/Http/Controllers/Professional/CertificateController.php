<?php
namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\OldCertificateRequest;
use App\Models\ProfessionalInfo;
use App\Services\Professional\CertificateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller {
    public function __construct(protected CertificateService $services) {
    }

    public function list(Request $request) {
        $data['page_title'] = 'My Certificates';
        $data['nav'] = 'certificate';
        $data['sub_nav'] = 'list';
        // $professional = Auth::guard('professional')->user();
        $professional_id = auth()->guard('professional')->id();

        // Get all regular certificates
        $certificates = 
        // Get all old certificate requests
        // $oldCertificateRequests = OldCertificateRequest::where('professional_id', $professional->id)
        //     ->with(['program', 'level'])
        //     ->orderBy('created_at', 'desc')
        //     ->get();

        $data['certificates'] = Certificate::where('professional_id', $professional_id)->with(['program', 'level'])->orderBy('created_at', 'desc')->get();

        // $data['old_certificate_requests'] = $oldCertificateRequests;

        return view('professional.certificate.list', $data);
    }
}