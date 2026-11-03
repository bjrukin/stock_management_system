<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\University\StoreRequest;
use App\Models\University;
use App\Services\Admin\UniversityService;
use Illuminate\Http\Request;

class UniversityController extends Controller
{

    public function __construct(protected UniversityService $service)
    {
    }

    public function list(Request $request)
    {
        
        $data['nav'] = 'collegeuniversity';
        $data['sub_nav'] = 'university';
        $data['page_title'] = 'University';
        if ($request->ajax()) {
            return $this->service->list();
        }
        return view('admin.university.list', $data);
    }
    public function status(Request $request)
    {
        return $this->service->status($request);
    }
    public function addEdit(Request $request)
    {
        $id = ($request->id) ? $request->id : 0;
        $data['nav'] = 'collegeuniversity';
        $data['sub_nav'] = 'university';
        $data['page_title'] =  ($id == 0) ? "Add University" : "Edit University";
        $data['action'] = route('admin.university.store');
        $data['row'] = University::where('id', $id)->first();
        return view('admin.university.form', $data);
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
