<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Permission\StoreRequest;
use App\Models\Module;
use App\Models\Permission;
use App\Models\Role;
use App\Services\Admin\PermissionService;
use Illuminate\Http\Request;

class PermissionController extends Controller
{

    public function __construct(protected PermissionService $service)
    {
    }

    public function list(Request $request)
    {
        
        $data['nav'] = 'rolepermission';
        $data['sub_nav'] = 'permission';
        $data['page_title'] = 'Permission';
        if ($request->ajax()) {
            return $this->service->list();
        }
        return view('admin.permission.list', $data);
    }
    public function addEdit(Request $request)
    {
        $role_id = ($request->role_id) ? $request->role_id : 0;
        $data['nav'] = 'rolepermission';
        $data['sub_nav'] = 'permission';
        $data['page_title'] =  ($role_id == 0) ? "Add Permission" : "Edit Permission";
        $data['action'] = route('admin.permission.store');
        $data['row'] = Permission::where('role_id', $role_id)->first();
        $data['roles'] = Role::where('id', '<>', 1)->orderBy('name', 'asc')->get();
        $data['modules'] = Module::where('id', '<>', 1)->orderBy('name', 'asc')->get();
        $data['count'] = 1;
        $data['permissions'] = ['list', 'create', 'edit', 'delete']; // available options
        $data['selectedPermissions'] =   $data['row']->name ?? [];
        return view('admin.permission.form', $data);
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
