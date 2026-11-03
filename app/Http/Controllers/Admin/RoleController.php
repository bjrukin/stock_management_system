<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Role\StoreRequest;
use App\Models\Role;
use App\Services\Admin\RoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{

    public function __construct(protected RoleService $service)
    {
    }

    public function list(Request $request)
    {
        
        $data['nav'] = 'rolepermission';
        $data['sub_nav'] = 'role';
        $data['page_title'] = 'Role';
        if ($request->ajax()) {
            return $this->service->list();
        }
        return view('admin.role.list', $data);
    }
    public function addEdit(Request $request)
    {
        $id = ($request->id) ? $request->id : 0;
        $data['nav'] = 'rolepermission';
        $data['sub_nav'] = 'role';
        $data['page_title'] =  ($id == 0) ? "Add Role" : "Edit Role";
        $data['action'] = route('admin.role.store');
        $data['row'] = Role::where('id', $id)->first();
        return view('admin.role.form', $data);
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
