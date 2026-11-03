<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\College\StoreRequest;
use App\Models\College;
use App\Services\Admin\CollegeService;
use Illuminate\Http\Request;

class CollegeController extends Controller
{

    public function __construct(protected CollegeService $service)
    {
    }

    public function list(Request $request)
    {
        
        $data['nav'] = 'collegeuniversity';
        $data['sub_nav'] = 'college';
        $data['page_title'] = 'College';
        if ($request->ajax()) {
            return $this->service->list();
        }
        return view('admin.college.list', $data);
    }
    public function status(Request $request)
    {
        return $this->service->status($request);
    }
    public function addEdit(Request $request)
    {
        $id = ($request->id) ? $request->id : 0;
        $data['nav'] = 'collegeuniversity';
        $data['sub_nav'] = 'college';
        $data['page_title'] =  ($id == 0) ? "Add College" : "Edit College";
        $data['action'] = route('admin.college.store');
        $data['row'] = College::where('id', $id)->first();
        return view('admin.college.form', $data);
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
