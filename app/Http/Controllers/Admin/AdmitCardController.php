<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\Exam;
use App\Models\Level;
use App\Models\Program;
use App\Models\SubjectCommittee;
use App\Services\Admin\AdmitCardService;
use Illuminate\Http\Request;

class AdmitCardController extends Controller
{
    public function __construct(protected AdmitCardService $service) {
    }
    public function addEdit(Request $request)
    {
        $data['id'] = ($request->id) ? $request->id : 0;
        $data['levels'] = Level::where('status', 1)->whereNotIn('id', [4, 5])->get();
        $data['programs'] = Program::where('status', 1)->get();
        $data['subject_committees'] = SubjectCommittee::where('status', 1)->get();

        $data['nav'] = 'admitcard';
        $data['sub_nav'] = 'admitcard';
        $data['page_title'] =  "Generate admitcard";
        $data['action'] = route('admin.admitcard.store');
        return view('admin.admitcard.form', $data);
    }

    public function store(Request $request)
    {
        $this->service->generateAdmitCard($request);
        $response['message'] = 'Status updated successfully.';
        $response['error'] = null;
        $response['status'] = 201;
        return response()->json($response, $response['status']);
        
    }

}
