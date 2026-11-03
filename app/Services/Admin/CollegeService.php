<?php
namespace App\Services\Admin;

use App\Models\College;
use Yajra\DataTables\Facades\DataTables;

class CollegeService {

    public function list() {
        try {
            $data = College::select('*');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    $checked = $row->status == 1 ? 'checked' : '';
                    $html = '<label class="switch">
                                <input type="checkbox" class="switch-input switch-status"
                                    data-id="' . $row->id . '" 
                                    data-status-value="' . $row->status . '" ' . $checked . '>
                                <span class="switch-label" data-on="Show" data-off="Hide"></span>
                                <span class="switch-handle"></span>
                            </label>';
                    return $html;
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    if(auth()->guard('admin')->user()->hasPermission('College', 'edit')) {
                        $editUrl = route('admin.college.addEdit', ['id' => $row->id]);
                        $btn .= '<a href="'.$editUrl.'"><span
                        class="mat_sym-icon material-symbols-outlined"><i class="mdi mdi-pencil-outline"></i></span></a>';
                    }
                    if(auth()->guard('admin')->user()->hasPermission('College', 'delete')) {
                        $btn .= ' <a href="javascript:void(0)" onclick="deleteData(' . $row->id . ')"><span class="mat_sym-icon material-symbols-outlined"><i class="mdi mdi-delete-forever-outline"></i> </span>
                        
                       </a>';
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
            College::where('id', $request->id)
                ->update([
                    $request->field_name => $request->val,
                ]);
            if ($request->val == 1) {
                $response['message'] = 'College enabled successfully';
            } else {
                $response['message'] = 'College disabled successfully';
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
            $college = $request['id'] ? College::firstOrNew(['id' => $request['id']]) : new College;
            $college->name = $request['name'];
            $college->save();
            $response['message'] = ($request['id']) ? 'College updated successfully.' : 'College added successfully.';
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
            College::where('id', $id)->delete();
            $response['message'] = 'College deleted successfully.';
            $response['error'] = null;
            $response['status'] = 200;
            return response()->json($response, $response['status']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

}