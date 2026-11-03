<?php
namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use App\Http\Requests\Professional\Exam\ApplicationRequest;
use App\Models\Applicant;
use App\Models\Exam;
use App\Models\Level;
use App\Models\Professional;
use App\Models\ProfessionalInfo;
use App\Models\Program;
use App\Services\Professional\ExamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class ExamController extends Controller {

    public function __construct(protected ExamService $service) {
    }
    public function index() {
        $data['nav'] = 'exam';
        $data['sub_nav'] = '';
        $data['page_title'] = "Apply Exam";
        $data['applicant'] = Applicant::where('professional_id', Auth::guard('professional')->id())->first();

        $data['result'] = $this->service->list();
        return view('professional.exam.index', $data);
    }
    public function tslc() {
        $data['nav'] = 'tslc';
        $data['sub_nav'] = '';
        $data['page_title'] = "TSLC Licenced Voucher";
        $data['applicant'] = Applicant::where('professional_id', Auth::guard('professional')->id())->first();

        $data['result'] = $this->service->list();
        return view('professional.exam.tslc', $data);
    }

    public function history() {
        $data['nav'] = 'examhistory';
        $data['sub_nav'] = '';
        $data['page_title'] = "Exam History";

        $exams = Applicant::where('professional_id', Auth::guard('professional')->id())
            ->with(['exam', 'applicant_logs'])
            ->orderBy('id', 'desc')
            ->get();

        $processedExams = $exams->map(function ($exam) {
            $states = ['operator', 'officer', 'registrar', 'subject_committee', 'exam_committee', 'council'];
            $status = 'accepted';
            $timeline = [];

            $resultStatus = optional($exam->exam)->result_status;

            foreach ($states as $state) {
                $stateStatus = $status;

                if ($exam->state === $state) {
                    $stateStatus = $exam->status;
                }

                $stateIndex = array_search($state, $states);
                $currentStateIndex = array_search($exam->state, $states);

                if (
                    $resultStatus == 0 &&
                    in_array($state, ['exam_committee', 'council'])
                ) {
                    $stateStatus = 'progress';
                }

                $logs = collect();
                $shouldHideLogs = ($resultStatus == 0 && in_array($state, ['exam_committee', 'council']));

                if (! $shouldHideLogs) {
                    $logs = $exam->applicant_logs->where('state', $state)->map(function ($log) {
                        return [
                            'timestamp' => $log->created_at->format('F j, Y H:i:s'),
                            'remarks'   => $log->remarks,
                        ];
                    });
                }

                // Show "Under Review" message for operator if status is progress
                if ($logs->isEmpty() && $state === 'operator' && $stateStatus === 'progress') {
                    $logs = collect([['timestamp' => null, 'remarks' => 'Under Review']]);
                }

                $timeline[] = [
                    'state'  => $state,
                    'status' => $stateStatus,
                    'logs'   => $logs,
                ];

                if ($exam->state === $state) {
                    $status = '';
                }
            }

            return [
                'exam_name'    => optional($exam->exam)->name ?? 'Exam',
                'applied_date' => $exam->created_at->format('F j, Y'),
                'timeline'     => $timeline,
            ];
        });

        $data['exams'] = $processedExams;
        $data['has_exams'] = $processedExams->isNotEmpty();

        return view('professional.exam.history', $data);
    }

    public function application(Request $request) {
        $data['nav'] = 'exam';
        $data['sub_nav'] = '';
        $data['page_title'] = "License Application Form";
        $isF = $request->isForeign;
        $data['applicant'] = ProfessionalInfo::where('professional_id', Auth::guard('professional')->id())->first();
        if (! $data['applicant']) {
            return redirect()->route('professional.profile.personal')->with('message', 'Please fill you profile first then only you
can apply exam.');
        }
        $data['exam_id'] = null;
        if ($request->id) {
            try {
                $exam_id = Crypt::decryptString($request->id);
                $exam = Exam::where('id', $exam_id)->first();
                if (! $exam) {
                    abort(404);
                }
            } catch (\Exception $e) {
                abort(404);
            }
            $data['exam_id'] = $exam_id;
            $data['exam'] = $exam;
        }
        $data['level_id'] = ($data['exam_id']) ? $data['applicant']->level_id : 4;
        if ($data['exam_id'] == null) {
            $data['programs'] = Program::where(['status' => 1, 'level_id' => $data['level_id']])->get();
            $data['levels'] = Level::where(['status' => 1, 'id' => 4])->get();
        } else {
            $data['programs'] = Program::where('status', 1)->where('level_id', '<>', 4)->get();
            $data['levels'] = Level::where('status', 1)->whereNotIn('id', [4, 5])->get();
        }
        return view('professional.exam.application', $data);
    }

    public function saveApplication(ApplicationRequest $request) {
        return $this->service->saveApplication($request->validated());
    }

    public function khaltiVerify(Request $request) {
        return $this->service->khaltiVerify($request);
    }


    public function voucherImageDelete(Request $request) {
        return $this->service->voucherImageDelete($request);
    }

    public function status() {
        $data['nav'] = 'status';
        $data['sub_nav'] = '';
        $data['page_title'] = "Exam Status";

        $data['professional'] = Professional::with([
            'info',
            'slc',
            'pcl',
            'tslc',
            'bachelor',
            'master',
            'qualifications',
        ])->where('id', Auth::guard('professional')->id())->first();

        $data['applicant'] = Applicant::where('professional_id', Auth::guard('professional')->id())
            ->orderBy('id', 'desc')
            ->first();

        return view("professional.exam.status", $data);
    }
}