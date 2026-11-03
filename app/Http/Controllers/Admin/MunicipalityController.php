<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Municipality\StoreRequest;
use App\Models\Municipality;
use App\Models\District;
use App\Services\Admin\MunicipalityService;
use Illuminate\Http\Request;

class MunicipalityController extends Controller
{

    public function __construct(protected MunicipalityService $service)
    {
    }

    public function list(Request $request)
    {
        
        $data['nav'] = 'address';
        $data['sub_nav'] = 'municipality';
        $data['page_title'] = 'Municipality';
        if ($request->ajax()) {
            return $this->service->list();
        }
        return view('admin.municipality.list', $data);
    }
    public function status(Request $request)
    {
        return $this->service->status($request);
    }
    public function addEdit(Request $request)
    {
        $id = ($request->id) ? $request->id : 0;
        $data['nav'] = 'address';
        $data['sub_nav'] = 'municipality';
        $data['page_title'] =  ($id == 0) ? "Add Municipality" : "Edit Municipality";
        $data['action'] = route('admin.municipality.store');
        $data['row'] = Municipality::where('id', $id)->first();
        $data['districts'] = District::where('status', 1)->get();
        return view('admin.municipality.form', $data);
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
