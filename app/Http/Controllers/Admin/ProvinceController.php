<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Province\StoreRequest;
use App\Models\Province;
use App\Services\Admin\ProvinceService;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{

    public function __construct(protected ProvinceService $service)
    {
    }

    public function list(Request $request)
    {
        
        $data['nav'] = 'address';
        $data['sub_nav'] = 'province';
        $data['page_title'] = 'Province';
        if ($request->ajax()) {
            return $this->service->list();
        }
        return view('admin.province.list', $data);
    }
    public function status(Request $request)
    {
        return $this->service->status($request);
    }
    public function addEdit(Request $request)
    {
        $id = ($request->id) ? $request->id : 0;
        $data['nav'] = 'address';
        $data['sub_nav'] = 'province';
        $data['page_title'] =  ($id == 0) ? "Add Province" : "Edit Province";
        $data['action'] = route('admin.province.store');
        $data['row'] = Province::where('id', $id)->first();
        return view('admin.province.form', $data);
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
