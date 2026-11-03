<?php
namespace App\Services\Admin;

use App\Models\Module;
use Yajra\DataTables\Facades\DataTables;

class ModuleService {

    public function list() {
        try {
            $data = Module::select('*');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '';
                    if(auth()->guard('admin')->user()->hasPermission('Module', 'edit')) {
                        $editUrl = route('admin.module.addEdit', ['id' => $row->id]);
                        $btn .= '<a href="'.$editUrl.'"><span
                        class="mat_sym-icon material-symbols-outlined"><i class="mdi mdi-pencil-outline"></i></span></a>';
                    }
                    if(auth()->guard('admin')->user()->hasPermission('Module', 'delete')) {
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
            $module = $request['id'] ? Module::firstOrNew(['id' => $request['id']]) : new Module;
            $module->name = $request['name'];
            $module->save();
            $response['message'] = ($request['id']) ? 'Module updated successfully.' : 'Module added successfully.';
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
            Module::where('id', $id)->delete();
            $response['message'] = 'Module deleted successfully.';
            $response['error'] = null;
            $response['status'] = 200;
            return response()->json($response, $response['status']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

}