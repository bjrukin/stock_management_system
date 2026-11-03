<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SubjectCommittee\StoreRequest;
use App\Models\SubjectCommittee;
use App\Services\Admin\SubjectCommitteeService;
use Illuminate\Http\Request;

class SubjectCommitteeController extends Controller
{

    public function __construct(protected SubjectCommitteeService $service)
    {
    }

    public function list(Request $request)
    {
        
        $data['nav'] = 'academics';
        $data['sub_nav'] = 'subjectcommittee';
        $data['page_title'] = 'Subject Committee';
        if ($request->ajax()) {
            return $this->service->list();
        }
        return view('admin.subjectcommittee.list', $data);
    }
    public function status(Request $request)
    {
        return $this->service->status($request);
    }
    public function addEdit(Request $request)
    {
        $id = ($request->id) ? $request->id : 0;
        $data['nav'] = 'academics';
        $data['sub_nav'] = 'subjectcommittee';
        $data['page_title'] =  ($id == 0) ? "Add Subject Committee" : "Edit Subject Committee";
        $data['action'] = route('admin.subjectcommittee.store');
        $data['row'] = SubjectCommittee::where('id', $id)->first();
        return view('admin.subjectcommittee.form', $data);
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
