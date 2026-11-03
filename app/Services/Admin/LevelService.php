<?php
namespace App\Services\Admin;

use App\Models\Level;
use Yajra\DataTables\Facades\DataTables;

class LevelService {

    public function list() {
        try {
            $data = Level::select('*')->orderBy('order', 'asc');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('order', function ($row) use($data) {
                    $total = $data->count();
                    $html = '<select name="order" class="form-select" onchange="changeOrder(' . $row->id . ', this)">';
                    for ($i = 1; $i <= $total; $i++) {
                        $selected = ($row->order == $i) ? 'selected' : '';
                        $html .= '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
                    }
                    $html .= '</select>';
                    return $html;
                })
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
                    if(auth()->guard('admin')->user()->hasPermission('Level', 'edit')) {
                        $editUrl = route('admin.level.addEdit', ['id' => $row->id]);
                        $btn .= '<a href="'.$editUrl.'"><span
                        class="mat_sym-icon material-symbols-outlined"><i class="mdi mdi-pencil-outline"></i></span></a>';
                    }
                    if(auth()->guard('admin')->user()->hasPermission('Level', 'delete')) {
                        $btn .= ' <a href="javascript:void(0)" onclick="deleteData(' . $row->id . ')"><span class="mat_sym-icon material-symbols-outlined"><i class="mdi mdi-delete-forever-outline"></i></span></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['status', 'order', 'action'])
                ->make(true);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function order($request)
    {
        try {
            Level::where('id', $request->id)->update(['order' => $request->order]);
            $response['message'] = 'Order updated successfully';
            $response['error'] = null;
            $response['status'] = 200;
            return response()->json($response, $response['status']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    public function status($request)
    {
        try {
            Level::where('id', $request->id)
                ->update([
                    $request->field_name => $request->val,
                ]);
            if ($request->val == 1) {
                $response['message'] = 'Level enabled successfully';
            } else {
                $response['message'] = 'Level disabled successfully';
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
            $level = $request['id'] ? Level::firstOrNew(['id' => $request['id']]) : new Level;
            $level->name = $request['name'];
            $level->short_name_english = $request['short_name_english'];
            $level->short_name_nepali = $request['short_name_nepali'];
            $level->code = $request['code'];
            $level->order = $request['id'] ? $level->order : getMax('levels', 'order');
            $level->save();
            $response['message'] = ($request['id']) ? 'Level updated successfully.' : 'Level added successfully.';
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
            Level::where('id', $id)->delete();
            $response['message'] = 'Level deleted successfully.';
            $response['error'] = null;
            $response['status'] = 200;
            return response()->json($response, $response['status']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

}