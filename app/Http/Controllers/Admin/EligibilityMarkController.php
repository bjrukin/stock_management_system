<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EligibilityMark\StoreRequest;
use App\Models\Level;
use App\Models\EligibilityMark;
use App\Services\Admin\EligibilityMarkService;
use Illuminate\Http\Request;

class EligibilityMarkController extends Controller {

    public function __construct(protected EligibilityMarkService $service) {
    }

    public function list(Request $request) {

        $data['nav'] = 'academics';
        $data['sub_nav'] = 'eligibilitymark';
        $data['page_title'] = 'Eligibility Mark';
        if ($request->ajax()) {
            return $this->service->list();
        }
        return view('admin.eligibilitymark.list', $data);
    }
    public function addEdit(Request $request) {
        $id = ($request->id) ? $request->id : 0;
        $data['nav'] = 'academics';
        $data['sub_nav'] = 'eligibilitymark';
        $data['page_title'] = ($id == 0) ? "Add Eligibility Mark" : "Edit Eligibility Mark";
        $data['action'] = route('admin.eligibilitymark.store');
        $data['row'] = EligibilityMark::where('id', $id)->first();
        $data['levels'] = Level::where('status', 1)->get();
        return view('admin.eligibilitymark.form', $data);
    }

    public function store(StoreRequest $request) {
        return $this->service->store($request->validated());
    }

    public function delete(Request $request) {
        return $this->service->delete($request);
    }
}