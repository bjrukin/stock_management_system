<?php
namespace App\Services\Professional;

use App\Models\Certificate;
use App\Models\CertificateRequest;
use App\Models\Exam;
use App\Models\ForeignCertificateRequest;
use App\Traits\StoreImageTrait;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class CertificateService
{
    use StoreImageTrait;

    public function list()
    {
        try {
            $current_date = Carbon::now('UTC')->format("Y-m-d");
            $data['exams'] = Exam::where('status', 1)
                ->where('opening_date', '<=', $current_date)
                ->where('closing_date', '>=', $current_date)
                ->get();
            return $data;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function saveCertificateApply($request)
    {
        try {
            $user_id = Auth::guard('professional')->id();

            $existingApplication = CertificateRequest::where('symbol_number', $request['symbol_number'])->first();
            if ($existingApplication) {
                return response()->json([
                    'message' => 'You have already applied for certificate request.',
                    'error' => null,
                    'status' => 406,
                ], 406);
            }

            $certificateApply = new CertificateRequest();
            $voucher_image = null;

            if (!empty($request['voucher_image'])) {
                if (preg_match('#^data:image.*?base64,#', $request['voucher_image'])) {
                    $voucher_image = $this->StoreBase64Image($request['voucher_image'], '/student/voucher/');
                } else {
                    $voucher_image = $request['voucher_image'];
                }
            }

            $certificateApply->user_id = $request['user_id'];
            $certificateApply->exam_id = $request['exam_id'];
            $certificateApply->level_id = $request['level_id'];
            $certificateApply->program_id = $request['program_id'];
            $certificateApply->symbol_number = $request['symbol_number'];
            $certificateApply->amount = $request['amount'];
            $certificateApply->voucher_image = $voucher_image;

            $certificateApply->save();

            return response()->json([
                'data' => $certificateApply,
                'message' => 'Certificate Print Applied Successfully',
                'error' => null,
                'success' => true,
                'status' => 200,
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function searchCertificates(array $filters)
    {
        try {
            // Both name and certificate number must be provided
            if (empty($filters['name']) || empty($filters['cert_registration_number'])) {
                return ['error' => 'Both name and certificate number are required to perform search.'];
            }

            $query = Certificate::query();

            // Apply both conditions
            $query->whereRaw(
                "REPLACE(name, '  ', ' ') LIKE ?",
                ['%' . preg_replace('/\s+/', ' ', $filters['name']) . '%']
            )
                ->where('cert_registration_number', $filters['cert_registration_number']);

            $certificates = $query->with(['user.info', 'program', 'level'])->get();

            if ($certificates->isEmpty()) {
                return ['certificates' => collect()];
            }

            return ['certificates' => $certificates];

        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
