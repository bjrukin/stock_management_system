<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SubjectCommitteeUser\StoreRequest;
use App\Models\Admin;
use App\Models\SubjectCommittee;
use App\Models\SubjectCommitteeUser;
use App\Services\Admin\SubjectCommitteeUserService;
use Illuminate\Http\Request;

class SubjectCommitteeUserController extends Controller
{

    public function __construct(protected SubjectCommitteeUserService $service)
    {
    }

    public function list(Request $request)
    {
        
        $data['nav'] = 'address';
        $data['sub_nav'] = 'subjectcommitteeuser';
        $data['page_title'] = 'SubjectCommittee User';
        if ($request->ajax()) {
            return $this->service->list();
        }
        return view('admin.subjectcommitteeuser.list', $data);
    }
    public function status(Request $request)
    {
        return $this->service->status($request);
    }
    public function addEdit(Request $request)
    {
        $id = ($request->id) ? $request->id : 0;
        $data['nav'] = 'address';
        $data['sub_nav'] = 'subjectcommitteeuser';
        $data['page_title'] =  ($id == 0) ? "Add SubjectCommittee User" : "Edit SubjectCommittee User";
        $data['action'] = route('admin.subjectcommitteeuser.store');
        $data['row'] = SubjectCommitteeUser::where('id', $id)->first();
        $data['users'] = Admin::where('status', 1)->get();
        $data['subject_committees'] = SubjectCommittee::where('status', 1)->get();
        return view('admin.subjectcommitteeuser.form', $data);
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
