<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Applicant\ExportRequest;
use App\Http\Requests\Admin\Applicant\SaveStateRequest;
use App\Http\Requests\Admin\Applicant\SaveStatusRequest;
use App\Http\Requests\Admin\Applicant\StoreRequest;
use App\Models\Applicant;
use App\Models\Exam;
use App\Models\Level;
use App\Models\Program;
use App\Services\Admin\ApplicantService;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ApplicantController extends Controller {

    public function __construct(protected ApplicantService $service) {
    }

    public function list(Request $request) {
        $data['nav'] = 'applicant';
        $data['sub_nav'] = 'applicant';
        if($request->exam_id && $request->exam_id == 'tslc') {
            $data['exam'] = $request->exam_id ? Exam::where('id', $request->exam_id)->first() : Exam::orderBy('id', 'desc')->first();
            $data['exam_id'] = $request->exam_id ?? $data['exam']->id;
            $data['page_title'] = 'Applicants for T-SLC';
        } else {            
            $data['exam'] = $request->exam_id ? Exam::where('id', $request->exam_id)->first() : Exam::orderBy('id', 'desc')->first();
            $data['exam_id'] = $request->exam_id ?? $data['exam']->id;
            $data['page_title'] = $data['exam_id'] == 0 ? 'All Applicants' : 'Applicants for ' . ($data['exam']->name);
        }
        $data['level_id'] = $request->level_id ?? '';
        $data['program_id'] = $request->program_id ?? '';
        $data['state'] = $request->state ?? 0;
        $data['status'] = $request->status ?? 0;
        $data['exams'] = Exam::select('*')->orderBy('id', 'desc')->get();
        $data['levels'] = Level::select('*')->where('status', 1)->orderBy('order', 'desc')->get();
        $data['programs'] = Program::select('*')->orderBy('id', 'desc')->get();
        $data['search_form'] = 'search_list';
        if ($request->ajax()) {
            return $this->service->list($data['exam_id'], $data['level_id'], $data['status'], $data['program_id'], $data['state']);
        }
        return view('admin.applicant.list', $data);
    }

    public function new(Request $request) {
        $data['nav'] = 'applicant';
        $data['sub_nav'] = 'new';
        if($request->exam_id && $request->exam_id == 'tslc') {
            $data['exam'] = $request->exam_id ? Exam::where('id', $request->exam_id)->first() : Exam::orderBy('id', 'desc')->first();
            $data['exam_id'] = $request->exam_id ?? $data['exam']->id;
            $data['page_title'] = 'New Applicants for T-SLC';
        } else {            
            $data['exam'] = $request->exam_id ? Exam::where('id', $request->exam_id)->first() : Exam::orderBy('id', 'desc')->first();
            $data['exam_id'] = $request->exam_id ?? $data['exam']->id;
            $data['page_title'] = 'New Applicants for ' . $data['exam']->name;
        }
        $data['level_id'] = $request->level_id ?? '';
        $data['program_id'] = $request->program_id ?? '';
        $data['state'] = $request->state ?? (auth()->guard('admin')->user()->role_id > 2 ? strtolower(str_replace(' ', '_', auth()->guard('admin')->user()->role->name)) : null);
        $data['status'] = $request->status ?? 'progress';
        $data['exams'] = Exam::select('*')->orderBy('id', 'desc')->get();
        $data['levels'] = Level::select('*')->where('status', 1)->orderBy('order', 'desc')->get();
        $data['programs'] = Program::select('*')->orderBy('id', 'desc')->get();
        $data['search_form'] = 'search_new';
        if ($request->ajax()) {
            return $this->service->new($data['exam_id'], $data['level_id'], $data['status'], $data['program_id'], $data['state']);
        }
        return view('admin.applicant.new', $data);
    }

    public function pending(Request $request) {
        $data['nav'] = 'applicant';
        $data['sub_nav'] = 'pending';
        if($request->exam_id && $request->exam_id == 'tslc') {
            $data['exam'] = $request->exam_id ? Exam::where('id', $request->exam_id)->first() : Exam::orderBy('id', 'desc')->first();
            $data['exam_id'] = $request->exam_id ?? $data['exam']->id;
            $data['page_title'] = 'Pending Applicants for T-SLC';
        } else {            
            $data['exam'] = $request->exam_id ? Exam::where('id', $request->exam_id)->first() : Exam::orderBy('id', 'desc')->first();
            $data['exam_id'] = $request->exam_id ?? $data['exam']->id;
            $data['page_title'] = 'Pending Applicants for ' . $data['exam']->name;
        }
        $data['level_id'] = $request->level_id ?? '';
        $data['program_id'] = $request->program_id ?? '';
        $data['state'] = $request->state ?? (auth()->guard('admin')->user()->role_id > 2 ? strtolower(str_replace(' ', '_', auth()->guard('admin')->user()->role->name)) : null);
        $data['status'] = $request->status ?? 'pending';
        $data['exams'] = Exam::select('*')->orderBy('id', 'desc')->get();
        $data['levels'] = Level::select('*')->where('status', 1)->orderBy('order', 'desc')->get();
        $data['programs'] = Program::select('*')->orderBy('id', 'desc')->get();
        $data['search_form'] = 'search_pending';
        if ($request->ajax()) {
            return $this->service->pending($data['exam_id'], $data['level_id'], $data['program_id'], $data['state']);
        }
        return view('admin.applicant.pending', $data);
    }

    public function approved(Request $request) {
        $data['nav'] = 'applicant';
        $data['sub_nav'] = 'approved';
        if($request->exam_id && $request->exam_id == 'tslc') {
            $data['exam'] = $request->exam_id ? Exam::where('id', $request->exam_id)->first() : Exam::orderBy('id', 'desc')->first();
            $data['exam_id'] = $request->exam_id ?? $data['exam']->id;
            $data['page_title'] = 'Approved Applicants for T-SLC';
        } else {            
            $data['exam'] = $request->exam_id ? Exam::where('id', $request->exam_id)->first() : Exam::orderBy('id', 'desc')->first();
            $data['exam_id'] = $request->exam_id ?? $data['exam']->id;
            $data['page_title'] = 'Approved Applicants for ' . $data['exam']->name;
        }
        $data['level_id'] = $request->level_id ?? '';
        $data['program_id'] = $request->program_id ?? '';
        $data['exams'] = Exam::select('*')->orderBy('id', 'desc')->get();
        $data['levels'] = Level::select('*')->where('status', 1)->orderBy('order', 'desc')->get();
        $data['programs'] = Program::select('*')->orderBy('id', 'desc')->get();
        $data['search_form'] = 'search_approved';
        if ($request->ajax()) {
            return $this->service->approved($data['exam_id'], $data['level_id'], $data['program_id']);
        }
        return view('admin.applicant.approved', $data);
    }

    public function rejected(Request $request) {
        $data['nav'] = 'applicant';
        $data['sub_nav'] = 'rejected';       
        if($request->exam_id && $request->exam_id == 'tslc') {
            $data['exam'] = $request->exam_id ? Exam::where('id', $request->exam_id)->first() : Exam::orderBy('id', 'desc')->first();
            $data['exam_id'] = $request->exam_id ?? $data['exam']->id;
            $data['page_title'] = 'Rejected Applicants for T-SLC';
        } else {            
            $data['exam'] = $request->exam_id ? Exam::where('id', $request->exam_id)->first() : Exam::orderBy('id', 'desc')->first();
            $data['exam_id'] = $request->exam_id ?? $data['exam']->id;
            $data['page_title'] = 'Rejected Applicants for ' . $data['exam']->name;
        }
        $data['level_id'] = $request->level_id ?? '';
        $data['program_id'] = $request->program_id ?? '';
        $data['exams'] = Exam::select('*')->orderBy('id', 'desc')->get();
        $data['levels'] = Level::select('*')->where('status', 1)->orderBy('order', 'desc')->get();
        $data['programs'] = Program::select('*')->orderBy('id', 'desc')->get();
        $data['search_form'] = 'search_rejected';
        if ($request->ajax()) {
            return $this->service->rejected($data['exam_id'], $data['level_id'], $data['program_id']);
        }
        return view('admin.applicant.rejected', $data);
    }

    public function export(ExportRequest $request) {
        return $this->service->export($request->validated());
    }

    public function addEdit(Request $request) {
        $id = ($request->id) ? $request->id : 0;
        $data['nav'] = 'applicant';
        $data['sub_nav'] = 'applicant';
        $data['page_title'] = "Edit Application";
        $data['action'] = route('admin.applicant.store');
        $data['row'] = Applicant::where('id', $id)->first();
        $data['exams'] = Exam::all();
        $data['levels'] = Level::where('id', '<>', 5)->orderBy('order', 'desc')->get();
        $data['programs'] = Program::all();
        if ($data['row']) {
            return view('admin.applicant.form', $data);
        } else {
            abort(404);
        }
    }

    public function store(StoreRequest $request) {
        return $this->service->store($request->validated());
    }
    public function delete(Request $request) {
        return $this->service->delete($request);
    }

    public function saveStatus(SaveStatusRequest $request) {
        return $this->service->saveStatus($request->validated());
    }

    public function saveState(SaveStateRequest $request) {
        return $this->service->saveState($request->validated());
    }

    public function admitCard($applicant_id)
    {
        $data['nav'] = 'applicant';
        $data['sub_nav'] = 'applicant';
        $data['page_title'] = 'Admin card';
        $data['applicant'] = Applicant::where('id', $applicant_id)->orderBy('id', 'desc')->first();
        if ($data['applicant']) {
            $data['qr'] = QrCode::size(120)->generate(
                "Name: {$data['applicant']->professional->info->full_name}\n" .
                "Symbol Number: {$data['applicant']->admit_card?->symbol_number}"
            );
            return view('admin.applicant.admit_card', $data);
        } else {
            abort(404);
        }
    }

}
