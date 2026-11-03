<?php
namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use App\Http\Requests\Professional\Profile\BachelorRequest;
use App\Http\Requests\Professional\Profile\GuardianRequest;
use App\Http\Requests\Professional\Profile\MasterRequest;
use App\Http\Requests\Professional\Profile\PclRequest;
use App\Http\Requests\Professional\Profile\PersonalRequest;
use App\Http\Requests\Professional\Profile\SlcRequest;
use App\Http\Requests\Professional\Profile\TslcRequest;
use App\Models\Applicant;
use App\Models\College;
use App\Models\District;
use App\Models\Level;
use App\Models\Municipality;
use App\Models\ProfessionalInfo;
use App\Models\ProfessionalQualification;
use App\Models\Program;
use App\Models\Province;
use App\Models\University;
use App\Services\Professional\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller {

    public function __construct(protected ProfileService $service) {
    }
    public function personal() {
        $data['nav'] = 'profile';
        $data['sub_nav'] = 'personal';
        $data['page_title'] = 'Personal Information';
        $data['professional'] = Auth::guard('professional')->user();
        $data['levels'] = Level::where('status', 1)->where('id', '<>', 5)->get();
        $data['programs'] = Program::where('status', 1)->get();
        $data['provinces'] = Province::where('status', 1)->get();
        $data['districts'] = District::where('status', 1)->get();
        $data['municipalities'] = Municipality::where('status', 1)->get();
        return view('professional.profile.personal', $data);
    }

    public function savePersonal(PersonalRequest $request) {
        return $this->service->savePersonal($request->validated());
    }
    public function imageDelete(Request $request) {
        return $this->service->imageDelete($request);
    }

    public function guardian() {
        $data['nav'] = 'profile';
        $data['sub_nav'] = 'guardian';
        $data['page_title'] = 'Guardian Information';
        $data['professional'] = ProfessionalInfo::where('professional_id', Auth::guard('professional')->id())->first();
        if (! $data['professional']) {
            return redirect()->route('professional.profile.personal');
        }
        return view('professional.profile.guardian', $data);
    }

    public function saveGuardian(GuardianRequest $request) {
        return $this->service->saveGuardian($request->validated());
    }

    public function slc() {
        $data['nav'] = 'profile';
        $data['sub_nav'] = 'college';
        $data['professional'] = Auth::guard('professional')->user();
        $data['colleges'] = College::all();
        $data['page_title'] = 'SLC Information';
        $data['level_id'] = 5;
        $data['professional_qualification'] = ProfessionalQualification::where(['professional_id' => Auth::guard('professional')->id(), 'level_id' => $data['level_id']])->first();
        $data['return_url'] = ($data['professional']->info->level_id == 4) ? route('professional.profile.tslc') : route('professional.profile.pcl');
        return view('professional.profile.slc', $data);
    }

    public function saveSlc(SlcRequest $request) {
        return $this->service->saveSlc($request->validated());
    }

    public function imageQualificationDelete(Request $request) {
        return $this->service->imageQualificationDelete($request);
    }
    
    public function tslc() 
    {
        $data['nav'] = 'profile';
        $data['sub_nav'] = 'college';
        $data['professional'] = Auth::guard('professional')->user();
        $data['colleges'] = College::all();
        $data['page_title'] = 'TSLC Information';
        $data['level_id'] = 4;
        $data['professional_qualification'] = ProfessionalQualification::where(['professional_id' => Auth::guard('professional')->id(), 'level_id' => $data['level_id']])->first();
        $data['applicant'] = Applicant::where('professional_id', Auth::Guard('professional')->id())->whereHas('exam', function ($qry) {
            $qry->where('status', 1);
        })->orderBy('id', 'DESC')->first();
        if (! $data['applicant']) {
            $data['applicant'] = Applicant::where('professional_id', Auth::Guard('professional')->id())->whereNull('exam_id')->first();
        }
        if (isset($data['applicant']) && $data['applicant']->status == 'rejected') {
            $data['return_url'] = route('professional.profile.voucher');
        } else {
            $data['return_url'] = route('professional.exam.dashboard');
        }
        return view('professional.profile.tslc', $data);
    }

    public function saveTslc(TslcRequest $request) {
        return $this->service->saveTslc($request->validated());
    }

    public function pcl() {
        $data['nav'] = 'profile';
        $data['sub_nav'] = 'college';
        $data['professional'] = Auth::guard('professional')->user();
        $data['colleges'] = College::all();
        $data['page_title'] = 'PCL/+2 Information';
        $data['level_id'] = 3;
        $data['professional_qualification'] = ProfessionalQualification::where(['professional_id' => Auth::guard('professional')->id(), 'level_id' => $data['level_id']])->first();
        $data['applicant'] = Applicant::where('professional_id', Auth::Guard('professional')->id())->whereHas('exam', function ($qry) {
            $qry->where('status', 1);
        })->orderBy('id', 'DESC')->first();
        if (isset($data['applicant']) && $data['applicant']->status == 'rejected') {
            $data['return_url'] = ($data['professional']->info->level_id == 2 || $data['professional']->info->level_id == 1) ? route('professional.profile.bachelor') : route('professional.profile.voucher');
        } else {
            $data['return_url'] = ($data['professional']->info->level_id == 2 || $data['professional']->info->level_id == 1) ? route('professional.profile.bachelor') : route('professional.exam.dashboard');
        }
        return view('professional.profile.pcl', $data);
    }
    public function savePcl(PclRequest $request) {
        return $this->service->savePcl($request->validated());
    }

    public function bachelor() {
        $data['nav'] = 'profile';
        $data['sub_nav'] = 'college';
        $data['professional'] = Auth::guard('professional')->user();
        $data['colleges'] = College::all();
        $data['universities'] = University::all();
        $data['page_title'] = 'Bachelor Information';
        $data['level_id'] = 2;
        $data['professional_qualification'] = ProfessionalQualification::where(['professional_id' => Auth::guard('professional')->id(), 'level_id' => $data['level_id']])->first();
        $data['applicant'] = Applicant::where('professional_id', Auth::Guard('professional')->id())->whereHas('exam', function ($qry) {
            $qry->where('status', 1);
        })->orderBy('id', 'DESC')->first();
        if (isset($data['applicant']) && $data['applicant']->status == 'rejected') {
            $data['return_url'] = ($data['professional']->info->level_id == 1) ? route('professional.profile.master') : route('professional.profile.voucher');
        } else {
            $data['return_url'] = ($data['professional']->info->level_id == 1) ? route('professional.profile.master') : route('professional.exam.dashboard');
        }
        return view('professional.profile.bachelor', $data);
    }
    public function saveBachelor(BachelorRequest $request) {
        return $this->service->saveBachelor($request->validated());
    }

    public function master() 
    {
        $data['nav'] = 'profile';
        $data['sub_nav'] = 'college';
        $data['professional'] = Auth::guard('professional')->user();
        $data['colleges'] = College::all();
        $data['page_title'] = 'Master Information';
        $data['universities'] = University::all();
        $data['level_id'] = 1;
        $data['professional_qualification'] = ProfessionalQualification::where(['professional_id' => Auth::guard('professional')->id(), 'level_id' => $data['level_id']])->first();
        $data['applicant'] = Applicant::where('professional_id', Auth::Guard('professional')->id())->whereHas('exam', function ($qry) {
            $qry->where('status', 1);
        })->orderBy('id', 'DESC')->first();
        if (isset($data['applicant']) && $data['applicant']->status == 'rejected') {
            $data['return_url'] = route('professional.profile.voucher');
        } else {
            $data['return_url'] = route('professional.exam.dashboard');
        }
        return view('professional.profile.master', $data);
    }
    public function saveMaster(MasterRequest $request) {
        return $this->service->saveMaster($request->validated());
    }

    public function voucher() {
        $data['nav'] = 'profile';
        $data['sub_nav'] = 'voucher';
        $data['professional'] = Auth::guard('professional')->user();
        $data['colleges'] = College::all();
        $data['page_title'] = 'Voucher';
        $data['universities'] = University::all();
        $data['applicant'] = Applicant::where('professional_id', Auth::Guard('professional')->id())->whereHas('exam', function ($qry) {
            $qry->where('status', 1);
        })->orderBy('id', 'DESC')->first();
        return view('professional.profile.voucher', $data);
    }
    public function saveVoucher(VoucherRequest $request) {
        return $this->service->saveVoucher($request->validated());
    }
    public function voucherImageDelete(Request $request) {
        return $this->service->voucherImageDelete($request);
    }

}