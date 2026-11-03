<?php
namespace App\Services\Professional;

use App\Models\Applicant;
use App\Models\Exam;
use App\Traits\StoreImageTrait;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class ExamService {
    use StoreImageTrait;
    public function list() {
        try {
            $current_date = Carbon::now('UTC')->format("Y-m-d");
            $data['exams'] = Exam::select('*')->where('status', 1)->where('opening_date', '<=', $current_date)->where('closing_date', '>=', $current_date)->get();
            return $data;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function saveApplication($request) {
        try {
            $professional_id = Auth::guard('professional')->id();
            $existing_apply = Applicant::where(['professional_id' => $professional_id, 'exam_id' => $request['exam_id'], 'payment_success' => 1])  
                ->whereIn('status', ['progress', 'accepted', 're-exam', 'onhold', 're-check'])
                ->first();
            if ($existing_apply) {
                $response['data'] = null;
                $response['message'] = null;
                $response['error'] = 'You have already applied for this program.';
                $response['status'] = 422;
                return response()->json($response, $response['status']);
            }
            $applicant = $this->storeApplicant($request);
            if ($request['payment_by'] == 'Khalti') {
                return $this->khatiPayemnt($applicant);
            }
            return response()->json([
                'data'    => $applicant,
                'success' => true,
                'message' => 'Exam Applied successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function storeApplicant($request) {        
        $professional_id = Auth::guard('professional')->id();
        if (isset($request['voucher_image']) && preg_match('#^data:image.*?base64,#', $request['voucher_image'])) {
            $voucher_image = $this->StoreBase64Image($request['voucher_image'], 'student', 'voucher_' . time());
        } else {
            $voucher_image = $request['voucher_image'] ?? null;
        }
        if (isset($request['biometric_photo']) && preg_match('#^data:image.*?base64,#', $request['biometric_photo'])) {
            $biometric_photo = $this->StoreBase64Image($request['biometric_photo'], 'student', 'biometric_' . time());
        } else {
            $biometric_photo = $request['biometric_photo'] ?? null;
        }

        $exam_status = Applicant::where(['professional_id' => $professional_id, 'is_passed' => '0', 'is_admit_card_generate' => 1])->orderBy('created_at', 'desc')->first();
        $applicant = new Applicant();
        $applicant->professional_id = $professional_id;
        $applicant->exam_id = $request['exam_id'];
        $applicant->level_id = $request['level'];
        $applicant->program_id = $request['program'];
        $applicant->voucher_image = $voucher_image;
        $applicant->biometric_photo = $biometric_photo;
        $applicant->status = $exam_status ? 're-exam' : 'progress';
        $applicant->state = 'operator';
        $applicant->attempt = $exam_status ? $exam_status->attempt + 1 : 1;
        $applicant->payment_by = $request['payment_by'];
        $applicant->payment_success = $request['payment_by'] == 'Bank' ? 1 : 0;
        $applicant->save();
        return $applicant;
    }

    public function khatiPayemnt($applicant)
    {
        if($applicant->exam) {
            $purchase_order_name = $applicant->exam->name;
            $exam_fee = $applicant->exam->exam_fee;
        } else {
            $purchase_order_name = 'TSLC Licenced';
            $exam_fee = 3000;
        }
        $mode = env('KHALTI_MODE', 'live');
        $base_url = config('khalti.' . $mode . '.base_url') . '/epayment/initiate/';
        // $amount = ($mode == 'live') ?  $exam_fee * 100 : 100; // amount in paisa
        $amount = $exam_fee * 100; // amount in paisa
        $parameter = [
            "return_url"          => route('professional.exam.khalti.verify'),
            "website_url"         => env('APP_URL'),
            "amount"              => $amount, 
            "purchase_order_id"   => 'Applicant-'.$applicant->id,
            "purchase_order_name" => $purchase_order_name,
            "customer_info"       => [
                "name"  => $applicant->professional->name,
                "email" => $applicant->professional->email,
                "phone" => $applicant->professional->phone,
            ],
        ];
        
        $res = Http::timeout(30)
            ->withHeaders([
                'Authorization' => 'key ' . config('khalti.'.$mode.'.secret_key'),
            ])
            ->post($base_url, $parameter);
        if ($res->successful()) {
            $data = $res->json();  
            if (isset($data['payment_url']) && isset($data['pidx'])) {
                return response()->json([
                    'redirect_url' => $data['payment_url'],
                    'success' => true,
                    'message' => 'Redirecting to Khalti payment',
                ], 200);
            }
        } else {
            $response['data'] = null;
            $response['message'] = null;
            $response['error'] = 'Sorry, we cannot process your payment at the moment.';
            $response['status'] = 422;
            return response()->json($response, $response['status']);
        }     
    }

    public function khaltiVerify($request)
    {
        if($request->status == 'Completed') {
            $purchase_order_id = $request->purchase_order_id;
            $applicant_id = explode('-', $purchase_order_id)[1];
            $professional_id = Auth::guard('professional')->id();
            $applicant = Applicant::where(['professional_id' => $professional_id, 'applicant_id' => $applicant_id])->first();
            if($applicant) {
                $applicant->payment_success = 1;
                $applicant->save();
            }
            return redirect()->route('professional.exam.status');
        }
        return redirect()->route('professional.exam.dashboard');
    }

    public function voucherImageDelete($request) {
        try {
            $field_name = $request->field_name;
            $ras = Applicant::where(['professional_id' => Auth::guard('professional')->id(), 'id' => $request->id])->first();
            if ($ras) {
                Storage::disk('public')->delete('/student/' . $ras->$field_name);
                $ras->$field_name = '';
                $ras->save();
            }
            return response()->json(['message' => 'Image deleted successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}