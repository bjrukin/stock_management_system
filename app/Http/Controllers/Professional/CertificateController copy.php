<?php
namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use App\Http\Requests\Professional\Certificate\ApplyRequest;
use App\Models\Applicant;
use App\Models\Certificate;
use App\Models\OldCertificateRequest;
use App\Models\ProfessionalInfo;
use App\Services\Professional\CertificateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    public function __construct(protected CertificateService $services)
    {
    }

    public function certificate()
    {
        $data['nav'] = 'certificate';
        $data['sub_nav'] = 'new_certificate_request';
        $data['page_title'] = 'New Certificate Request';
        $data['professional'] = ProfessionalInfo::where('professional_id', Auth::guard('professional')->id())->first();

        $data['applicant'] = Applicant::where('professional_id', Auth::guard('professional')->id())
            ->orderby('id', 'desc')
            ->first();

        $data['has_passed_exam'] = false;
        $data['symbol_number'] = null;

        if ($data['applicant']) {
            $data['has_passed_exam'] = $data['applicant']->is_passed == 1;
            if ($data['has_passed_exam'] && $data['applicant']->admit_card) {
                $data['symbol_number'] = $data['applicant']->admit_card->symbol_number;
            }
        }

        return view('professional.certificate.apply', $data);
    }
    public function saveCertificateApply(ApplyRequest $request)
    {
        return $this->services->saveCertificateApply($request->validated());
    }

    public function renewSearchPage(Request $request)
    {
        $data['page_title'] = 'Certificate Renewal Search';
        $data['nav'] = 'certificate';
        $data['sub_nav'] = 'verify_certificate ';

        $filters = [
            'name' => $request->input('name', null),
            'cert_registration_number' => $request->input('cert_registration_number', null),
        ];

        $data['loggedInUserInfo'] = ProfessionalInfo::where('professional_id', Auth::guard('professional')->id())->first();

        // Check if both fields are filled
        $bothFieldsFilled = !empty($filters['name']) && !empty($filters['cert_registration_number']);

        if ($bothFieldsFilled) {
            $result = $this->services->searchCertificates($filters);

            if (isset($result['error'])) {
                $data['message'] = $result['error'];
                $data['certificates'] = collect();
            } elseif (empty($result['certificates'])) {
                $data['certificates'] = collect();
            } else {
                $data['certificates'] = $result['certificates'];
            }
        } else {
            $data['certificates'] = collect();

            // Set a flag to show validation message if search was attempted but fields are incomplete
            if ($request->hasAny(['name', 'cert_registration_number']) && !$bothFieldsFilled) {
                $data['incomplete_search'] = true;
            }
        }

        return view('professional.certificate.renew_search', $data);
    }

    public function detail(Certificate $certificate)
    {
        $certificate->load('user', 'program', 'level');

        $page_title = 'Certificate Details';
        $nav = 'certificate';

        return view('professional.certificate.renew_detail', compact('certificate', 'page_title', 'nav'));
    }

    public function submitRenew(Request $request, Certificate $certificate)
    {
    }

    public function certificateList(Request $request)
    {
        $data['page_title'] = 'My Certificates';
        $data['nav'] = 'certificate';
        $data['sub_nav'] = 'certificate_list';

        $user = Auth::guard('professional')->user();

        // Get all regular certificates
        $regularCertificates = Certificate::where('user_id', $user->id)
            ->with(['program', 'level'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get all old certificate requests
        $oldCertificateRequests = OldCertificateRequest::where('user_id', $user->id)
            ->with(['program', 'level'])
            ->orderBy('created_at', 'desc')
            ->get();

        $data['regular_certificates'] = $regularCertificates;
        $data['old_certificate_requests'] = $oldCertificateRequests;
        $data['loggedInUserInfo'] = ProfessionalInfo::where('professional_id', $user->id)->first();

        return view('professional.certificate.certificate_list', $data);
    }
}
