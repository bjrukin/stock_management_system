<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Professional\BachelorRequest;
use App\Http\Requests\Admin\Professional\MasterRequest;
use App\Http\Requests\Admin\Professional\PclRequest;
use App\Http\Requests\Admin\Professional\ProfileRequest;
use App\Http\Requests\Admin\Professional\SlcRequest;
use App\Http\Requests\Admin\Professional\TslcRequest;
use App\Models\Applicant;
use App\Models\ApplicantLog;
use App\Models\College;
use App\Models\District;
use App\Models\Level;
use App\Models\Municipality;
use App\Models\Professional;
use App\Models\ProfessionalQualification;
use App\Models\Program;
use App\Models\Province;
use App\Models\Role;
use App\Models\University;
use App\Services\Admin\ProfessionalService;
use Illuminate\Http\Request;

class ProfessionalController extends Controller {

    public function __construct(protected ProfessionalService $service) {
    }
    public function list(Request $request) {
        $data['nav'] = 'user';
        $data['sub_nav'] = 'professional';
        $data['page_title'] = 'Professionals';
        if ($request->ajax()) {
            return $this->service->list();
        }
        return view('admin.professional.list', $data);
    }
    public function detail(Request $request) {
        $data['nav'] = 'applicant';
        $data['sub_nav'] = 'professional';
        $data['page_title'] = 'Professionals';
        $id = ($request->id) ? $request->id : 0;
        $data['professional'] = Professional::with(['slc', 'tslc', 'pcl', 'bachelor', 'master', 'qualifications'])->where('id', $id)->first();
        if ($data['professional']) {
            $data['applicant'] = Applicant::where('professional_id', $id)->orderby('id', 'desc')->first();
            $data['roles'] = Role::where('id', '>', 2)->get();
            $query = Applicant::where('professional_id', $id);
            $data['lastest_applicant'] = (clone $query)
                ->with('exam')
                ->whereHas('exam', fn($q) => $q->where('status', 1))
                ->latest()
                ->first() ?? $query->latest()->first();

            $applicant_id = $data['lastest_applicant']->id ?? "";
            $data['applicant_logs'] = ApplicantLog::select('*')
                ->where(['professional_id' => $id, 'applicant_id' => $applicant_id])
                ->get();

            $data['colors'] = [
                1 => '#7ce97c',
                2 => '#8deaed',
                3 => '#fff000',
                4 => '#ff8acc',
                5 => '#ffffff',
            ];
            return view('admin.professional.detail', $data);
        } else {
            abort(404);
        }
    }
    public function profile(Request $request) {
        $data['nav'] = 'user';
        $data['sub_nav'] = 'professional';
        $data['page_title'] = 'Edit Profile';
        $id = ($request->id) ? $request->id : 0;
        $data['professional'] = Professional::where('id', $id)->first();
        $data['action'] = route('admin.professional.save.profile');
        $data['levels'] = Level::where('status', 1)->where('id', '<>', 5)->get();
        $data['programs'] = Program::where('status', 1)->get();
        $data['provinces'] = Province::where('status', 1)->get();
        $data['districts'] = District::where('status', 1)->get();
        $data['municipalities'] = Municipality::where('status', 1)->get();
        if ($data['professional']) {
            return view('admin.professional.profile', $data);
        } else {
            abort(404);
        }
    }
    public function saveProfile(ProfileRequest $request) {
        return $this->service->saveProfile($request->validated());
    }

    public function status(Request $request) {
        return $this->service->status($request);
    }
    public function delete(Request $request) {
        return $this->service->delete($request);
    }

    public function imageProfileDelete(Request $request) {
        return $this->service->imageProfileDelete($request);
    }
    public function qualification($level, $professional_id) {
        $data['nav'] = 'user';
        $data['sub_nav'] = 'professional';
        $professional_id = $professional_id;
        $data['professional'] = Professional::where('id', $professional_id)->first();
        $data['level'] = Level::select('*')->where('route_name', $level)->first();
        $data['professional_qualification'] = ProfessionalQualification::where(['professional_id' => $professional_id, 'level_id' => $data['level']->id ?? 0])->first();
        $data['colleges'] = College::all();
        $data['universities'] = University::all();
        if ($data['professional'] && $data['level'] && $data['professional_qualification']) {
            $data['file_name'] = $level;
            $data['page_title'] = 'Edit ' . $data['level']->name . ' Qualification';
            $data['action'] = route('admin.professional.save.' . $data['level']->route_name);
            return view('admin.professional.qualification', $data);
        } else {
            abort(404);
        }
    }
    public function imageQualificationDelete(Request $request) {
        return $this->service->imageQualificationDelete($request);
    }
    public function saveSlc(SlcRequest $request) {
        return $this->service->saveSlc($request->validated());
    }
    public function saveTslc(TslcRequest $request) {
        return $this->service->saveTslc($request->validated());
    }
    public function savePcl(PclRequest $request) {
        return $this->service->savePcl($request->validated());
    }
    public function saveBachelor(BachelorRequest $request) {
        return $this->service->saveBachelor($request->validated());
    }
    public function saveMaster(MasterRequest $request) {
        return $this->service->saveMaster($request->validated());
    }

}