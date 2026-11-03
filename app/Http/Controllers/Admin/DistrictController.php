<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\District\StoreRequest;
use App\Models\District;
use App\Models\Province;
use App\Services\Admin\DistrictService;
use Illuminate\Http\Request;

class DistrictController extends Controller
{

    public function __construct(protected DistrictService $service)
    {
    }

    public function list(Request $request)
    {
        
        $data['nav'] = 'address';
        $data['sub_nav'] = 'district';
        $data['page_title'] = 'District';
        if ($request->ajax()) {
            return $this->service->list();
        }
        return view('admin.district.list', $data);
    }
    public function status(Request $request)
    {
        return $this->service->status($request);
    }
    public function addEdit(Request $request)
    {
        $id = ($request->id) ? $request->id : 0;
        $data['nav'] = 'address';
        $data['sub_nav'] = 'district';
        $data['page_title'] =  ($id == 0) ? "Add District" : "Edit District";
        $data['action'] = route('admin.district.store');
        $data['row'] = District::where('id', $id)->first();
        $data['provinces'] = Province::where('status', 1)->get();
        return view('admin.district.form', $data);
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
