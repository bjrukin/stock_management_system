<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Exam\StoreRequest;
use App\Models\AdmitCard;
use App\Models\Exam;
use App\Models\Applicant;
use App\Models\Program;
use App\Models\SubjectCommittee;
use App\Services\Admin\ExamService;
use Illuminate\Http\Request;

class ExamController extends Controller
{

    public function __construct(protected ExamService $service)
    {
    }

    public function list(Request $request)
    {
        $data['nav'] = 'exam';
        $data['sub_nav'] = 'exam';
        $data['page_title'] = 'Exam';
        if ($request->ajax()) {
            return $this->service->list();
        }
        return view('admin.exam.list', $data);
    }
    public function status(Request $request)
    {
        return $this->service->status($request);
    }
    public function addEdit(Request $request)
    {
        $id = ($request->id) ? $request->id : 0;
        $data['nav'] = 'exam';
        $data['sub_nav'] = 'exam';
        $data['page_title'] =  ($id == 0) ? "Add Exam" : "Edit Exam";
        $data['action'] = route('admin.exam.store');
        $data['row'] = Exam::where('id', $id)->first();
        return view('admin.exam.form', $data);
    }

    public function store(StoreRequest $request)
    {
        return $this->service->store($request->validated());
    }

    public function delete(Request $request)
    {
        return $this->service->delete($request);
    }

    public function examDetail(Request $request)
    {
        $id = $request->exam_id;
        $data['nav'] = 'exam_detail';
        $data['sub_nav'] = '';
        $data['page_title'] = "Exam Detail";
        $data['exam'] = Exam::where('id', $id)->first();
        $data['applicant_count'] = Applicant::where('exam_id', $id)->count();
        $data['admit_card_count'] = Applicant::where('exam_id', $id)->where('is_admit_card_generate', '1')->count();
        $data['pass_count'] = Applicant::where('exam_id', $id)->where('is_passed', '1')->count();
        $data['failed_count'] = Applicant::where('exam_id', $id)->where('is_admit_card_generate', '1')->where('is_passed', 0)->count();
        $data['subject_committees'] = SubjectCommittee::with([
            'programs' => function ($q) use ($id) {
                $q->where('has_exam', 1)
                ->withCount([
                    'applicant as exam_apply_count' => function ($q) use ($id) {
                        $q->where('exam_id', $id);
                    },
                    'admit_cards as admit_card_count' => function ($q) use ($id) {
                        $q->where('exam_id', $id);
                    },
                    'pass_applicant as pass_applicant_count' => function ($q) use ($id) {
                        $q->where('exam_id', $id);
                    },
                    'fail_applicant as fail_applicant_count' => function ($q) use ($id) {
                        $q->where('exam_id', $id);
                    },
                ])
                ->havingRaw(
                    'exam_apply_count > 0 
                    OR admit_card_count > 0 
                    OR pass_applicant_count > 0 
                    OR fail_applicant_count > 0'
                )
                ->orderBy('level_id');
            }
        ])
        ->get();

        return view('admin.exam.student_detail', $data);
    }

    public function routineForm(Request $request)
    {
        $id = $request->id;
        $data['row'] = $this->service->routineGenerate($request);
        $data['programs'] = Program::where('has_exam', 1)->where('status', 1)->get();
        $data['nav'] = 'exam';
        $data['sub_nav'] = 'exam';
        $data['page_title'] = "Generate Routine";
        $data['exam'] = Exam::where('id', $id)->first();
        $data['action'] = route('admin.exam.routine', ['id' => $id]);
        return view('admin.exam.routine_form', $data);
    }

    public function routineGenerate(Request $request)
    {
        // $id = $request->id;
        // $data['row'] = $this->service->routineGenerate($request);
        // $data['nav'] = 'exam';
        // $data['sub_nav'] = 'exam';
        // $data['page_title'] = "Generate Routine";
        // $data['exam'] = Exam::where('id', $id)->first();
        // $data['action'] = route('admin.exam.routineGenerate');
        // return view('admin.exam.routine_form', $data);
    }
}
