<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VerificationStep\StoreRequest;
use App\Models\VerificationStep;
use App\Models\Role;
use App\Services\Admin\VerificationStepService;
use Illuminate\Http\Request;

class VerificationStepController extends Controller
{

    public function __construct(protected VerificationStepService $service)
    {
    }

    public function list(Request $request)
    {
        
        $data['nav'] = 'rolepermission';
        $data['sub_nav'] = 'verificationstep';
        $data['page_title'] = 'VerificationStep';
        if ($request->ajax()) {
            return $this->service->list();
        }
        return view('admin.verificationstep.list', $data);
    }

    public function step(Request $request)
    {
        return $this->service->step($request);
    }
    public function addEdit(Request $request)
    {
        $id = ($request->id) ? $request->id : 0;
        $data['nav'] = 'rolepermission';
        $data['sub_nav'] = 'verificationstep';
        $data['page_title'] =  ($id == 0) ? "Add Verification Step" : "Edit Verification Step";
        $data['action'] = route('admin.verificationstep.store');
        $data['row'] = VerificationStep::where('id', $id)->first();
        $data['roles'] = Role::where('id', '<>', 1)->orderBy('id', 'asc')->get();
        return view('admin.verificationstep.form', $data);
    }

    public function store(StoreRequest $request)
    {
        return $this->service->store($request->validated());
    }

    public function delete(Request $request)
    {
        return $this->service->delete($request);
    }
}
