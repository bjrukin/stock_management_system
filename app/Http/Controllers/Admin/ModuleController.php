<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Module\StoreRequest;
use App\Models\Module;
use App\Services\Admin\ModuleService;
use Illuminate\Http\Request;

class ModuleController extends Controller
{

    public function __construct(protected ModuleService $service)
    {
    }

    public function list(Request $request)
    {
        
        $data['nav'] = 'rolepermission';
        $data['sub_nav'] = 'module';
        $data['page_title'] = 'Module';
        if ($request->ajax()) {
            return $this->service->list();
        }
        return view('admin.module.list', $data);
    }
    public function addEdit(Request $request)
    {
        $id = ($request->id) ? $request->id : 0;
        $data['nav'] = 'rolepermission';
        $data['sub_nav'] = 'module';
        $data['page_title'] =  ($id == 0) ? "Add Module" : "Edit Module";
        $data['action'] = route('admin.module.store');
        $data['row'] = Module::where('id', $id)->first();
        return view('admin.module.form', $data);
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
