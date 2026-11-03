<?php
namespace App\Services\Admin;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class UserService {

    public function list() {
        try {
            $adminTable = (new Admin)->getTable();
            $roleTable = (new Role)->getTable();
            $data = Admin::select($adminTable . '.id', $adminTable . '.name', $adminTable . '.email', $adminTable . '.phone', $adminTable . '.password_reference', $adminTable . '.status', $roleTable . '.name as role_name')->leftJoin($roleTable, $adminTable . '.role_id', '=', $roleTable . '.id')->where($adminTable . '.role_id', '<>', 1);
            return DataTables::of($data)
                ->addIndexColumn()
                ->filterColumn('role_name', function ($qry, $keyword) use ($roleTable) {
                    $qry->where($roleTable . '.name', 'LIKE', '%' . $keyword . '%');
                })
                ->addColumn('status', function ($row) {
                    $checked = $row->status == 1 ? 'checked' : '';
                    $html = '<label class="switch">
                                <input type="checkbox" class="switch-input switch-status"
                                    data-id="' . $row->id . '" 
                                    data-status-value="' . $row->status . '" ' . $checked . '>
                                <span class="switch-label" data-on="Active" data-off="Deactivate"></span>
                                <span class="switch-handle"></span>
                            </label>';
                    return $html;
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    if(auth()->guard('admin')->user()->hasPermission('User', 'edit')) {
                        $editUrl = route('admin.user.addEdit', ['id' => $row->id]);
                        $btn .= '<a href="'.$editUrl.'"><span
                        class="mat_sym-icon material-symbols-outlined"><i class="mdi mdi-pencil-outline"></i></span></a>';
                    }
                    if(auth()->guard('admin')->user()->hasPermission('User', 'delete')) {
                        $btn .= ' <a href="javascript:void(0)" onclick="deleteData(' . $row->id . ')"><span class="mat_sym-icon material-symbols-outlined"><i class="mdi mdi-delete-forever-outline"></i></span></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function status($request)
    {
        try {
            User::where('id', $request->id)
                ->update([
                    $request->field_name => $request->val,
                ]);
            if ($request->val == 1) {
                $response['message'] = 'User enabled successfully';
            } else {
                $response['message'] = 'User disabled successfully';
            }
            $response['error'] = null;
            $response['status'] = 200;
            return response()->json($response, $response['status']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function store($request) {
        try {
            $user = $request['id'] ? User::firstOrNew(['id' => $request['id']]) : new User;
            $user->role_id = $request['role'];
            $user->name = $request['name'];
            $user->phone = $request['phone'];
            $user->email = $request['email'];
            $user->username = $request['email'];
            $user->password = Hash::make($request['password']);
            $user->password_reference = $request['password'];
            $user->save();
            $response['message'] = ($request['id']) ? 'User updated successfully.' : 'User added successfully.';
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
            User::where('id', $id)->delete();
            $response['message'] = 'User deleted successfully.';
            $response['error'] = null;
            $response['status'] = 200;
            return response()->json($response, $response['status']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

}