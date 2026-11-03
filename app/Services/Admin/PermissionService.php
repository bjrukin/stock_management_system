<?php
namespace App\Services\Admin;

use App\Models\Permission;
use App\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class PermissionService {

    public function list() {
        try {
            $permissionTable = (new Permission)->getTable();
            $roleTable = (new Role)->getTable();
            $data = Permission::select($permissionTable . '.role_id', $roleTable . '.name as role_name')->leftJoin($roleTable, $permissionTable . '.role_id', '=', $roleTable . '.id')->groupBy($permissionTable . '.role_id');
            return DataTables::of($data)
                ->filterColumn('role_name', function ($qry, $keyword) use ($roleTable) {
                    $qry->where($roleTable . '.name', 'LIKE', '%' . $keyword . '%');
                })
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '';
                    if (auth()->guard('admin')->user()->hasPermission('Permission', 'edit')) {
                        $editUrl = route('admin.permission.addEdit', ['role_id' => $row->role_id]);
                        $btn .= '<a href="' . $editUrl . '"><span
                        class="mat_sym-icon material-symbols-outlined"><i class="mdi mdi-pencil-outline"></i></span></a>';
                    }
                    if (auth()->guard('admin')->user()->hasPermission('Permission', 'delete')) {
                        $btn .= ' <a href="javascript:void(0)" onclick="deleteData(' . $row->role_id . ')"><span class="mat_sym-icon material-symbols-outlined"><i class="mdi mdi-delete-forever-outline"></i></span></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function store($request) {
        try {
            Permission::where(['role_id' => $request['role']])->delete();
            foreach ($request['module'] as $key => $module) {
                $name = [$request['list'][$key] ?? null, $request['create'][$key] ?? null, $request['edit'][$key] ?? null, $request['delete'][$key] ?? null];
                $name = array_filter($name);
                $permission = new Permission;
                $permission->role_id = $request['role'];
                $permission->module_id = $module;
                $permission->name = $name;
                $permission->save();
            }
            $response['message'] = ($request['id']) ? 'Permission updated successfully.' : 'Permission added successfully.';
            $response['error'] = null;
            $response['status'] = 201;
            return response()->json($response, $response['status']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function delete($request) {
        try {
            $role_id = $request->role_id;
            Permission::where('role_id', $role_id)->delete();
            $response['message'] = 'Permission deleted successfully.';
            $response['error'] = null;
            $response['status'] = 200;
            return response()->json($response, $response['status']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

}