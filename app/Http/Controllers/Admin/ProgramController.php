<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Program\StoreRequest;
use App\Models\Level;
use App\Models\Program;
use App\Models\SubjectCommittee;
use App\Services\Admin\ProgramService;
use Illuminate\Http\Request;

class ProgramController extends Controller
{

    public function __construct(protected ProgramService $service)
    {
    }

    public function list(Request $request)
    {
        
        $data['nav'] = 'academics';
        $data['sub_nav'] = 'program';
        $data['page_title'] = 'Program';
        if ($request->ajax()) {
            return $this->service->list();
        }
        return view('admin.program.list', $data);
    }
    public function status(Request $request)
    {
        return $this->service->status($request);
    }
    public function addEdit(Request $request)
    {
        $id = ($request->id) ? $request->id : 0;
        $data['nav'] = 'academics';
        $data['sub_nav'] = 'program';
        $data['page_title'] =  ($id == 0) ? "Add Program" : "Edit Program";
        $data['action'] = route('admin.program.store');
        $data['row'] = Program::where('id', $id)->first();
        $data['levels'] = Level::where('status', 1)->get();
        $data['subject_committees'] = SubjectCommittee::where('status', 1)->get();
        return view('admin.program.form', $data);
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
