<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Kyc\SaveAllocateRequest;
use App\Models\Certificate;
use App\Models\Kyc;
use App\Models\Level;
use App\Models\Program;
use App\Services\Admin\KycService;
use Illuminate\Http\Request;

class KycController extends Controller {

    public function __construct(protected KycService $service) {
    }

    public function list(Request $request) {
        $data['nav'] = 'kyc';
        $data['sub_nav'] = 'kyc';
        $data['page_title'] = 'KYC';
        if ($request->ajax()) {
            return $this->service->list();
        }
        return view('admin.kyc.list', $data);
    }
    public function delete(Request $request) {
        return $this->service->delete($request);
    }

    public function certificate(Request $request) {
        $data['nav'] = 'kyc';
        $data['sub_nav'] = 'certificate';
        $data['page_title'] = 'KYC Certificate';
        $data['level_id'] = $request->level_id ?? '';
        $data['program_id'] = $request->program_id ?? '';
        $data['decision_date'] = $request->decision_date ?? '';
        $data['decision_dates'] = Certificate::select('decision_date')->distinct()->orderby('decision_date', 'desc')->get();
        $data['levels'] = Level::select('*')->where('status', 1)->where('id', '<', '5')->orderBy('order', 'desc')->get();
        $data['programs'] = Program::select('*')->orderBy('name', 'desc')->get();
        $data['is_printed'] = $request->is_printed ?? '';
        if ($request->ajax()) {
            return $this->service->certificate($data['is_printed'], $data['level_id'], $data['program_id'], $data['decision_date']);
        }
        return view('admin.kyc.certificate', $data);
    }

    public function allocate(Request $request) {
        $data['certificate'] = Certificate::where('id', $request->id)->first();
        return view('admin.kyc.modal.allocate', $data);
    }

    public function saveAllocate(SaveAllocateRequest $request) {
        return $this->service->saveAllocate($request->validated());
    }

    public function json(Request $request) {
        $term = $request['term']['term'];
        $response['message'] = '';
        $response['error'] = null;
        $response['status'] = 200;
        $response['data'] = Kyc::whereAny(['name', 'registration_number', 'symbol_number'], 'like', '%' . $term . '%')->limit(10)->get();
        return response()->json($response['data'], $response['status']);
    }

}