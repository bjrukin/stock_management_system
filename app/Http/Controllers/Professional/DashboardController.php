<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\ApplicantLog;

class DashboardController extends Controller
{
    public function index()
    {
        $data['nav'] = 'dashboard';
        $data['sub_nav'] = '';
        $data['page_title'] = "Dashboard";
        $professional_id = auth()->guard('professional')->id();
        $data['lastest_apply'] = Applicant::where('professional_id', $professional_id)->whereHas('exam', function ($qry) {
            $qry->where('status', 1); })->orderBy('id', 'desc')->first();
        if (!$data['lastest_apply']) {
            $data['lastest_apply'] = Applicant::where('professional_id', $professional_id)->whereNull('exam_id')->orderBy('id', 'desc')->first();
        }
        $data['applicant_logs'] = ApplicantLog::select('*')->where(['professional_id' => $professional_id, 'applicant_id' => $data['lastest_apply']?->id])->orderBy('id', 'asc')->get();
        return view('professional.dashboard.index', $data);
    }
}
