<?php
namespace App\Services\Admin;

use App\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RoleService {

    public function list() {
        try {
            $data = Role::select('*')->where('id','<>',1);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '';
                    if(auth()->guard('admin')->user()->hasPermission('Role', 'edit')) {
                        $editUrl = route('admin.role.addEdit', ['id' => $row->id]);
                        $btn .= '<a href="'.$editUrl.'"><span
                        class="mat_sym-icon material-symbols-outlined"><i class="mdi mdi-pencil-outline"></i></span></a>';
                    }
                    if(auth()->guard('admin')->user()->hasPermission('Role', 'delete')) {
                        $btn .= ' <a href="javascript:void(0)" onclick="deleteData(' . $row->id . ')"><span class="mat_sym-icon material-symbols-outlined"><i class="mdi mdi-delete-forever-outline"></i></span></a>';
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
            $role = $request['id'] ? Role::firstOrNew(['id' => $request['id']]) : new Role;
            $role->name = $request['name'];
            $role->save();
            $response['message'] = ($request['id']) ? 'Role updated successfully.' : 'Role added successfully.';
            $response['error'] = null;
            $response['status'] = 201;
            return response()->json($response, $response['status']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function delete($request) {
        try {
            $id = $request->id;
            Role::where('id', $id)->delete();
            $response['message'] = 'Role deleted successfully.';
            $response['error'] = null;
            $response['status'] = 200;
            return response()->json($response, $response['status']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

}