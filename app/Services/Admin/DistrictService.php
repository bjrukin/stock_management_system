<?php
namespace App\Services\Admin;

use App\Models\District;
use App\Models\Province;
use Yajra\DataTables\Facades\DataTables;

class DistrictService {

    public function list() {
        try {
            $districtTable = (new District)->getTable();
            $provinceTable = (new Province)->getTable();
            $data = District::select($districtTable . '.id', $districtTable . '.name', $districtTable . '.province_id', $districtTable . '.status', $provinceTable . '.name as province_name')->leftJoin($provinceTable, $districtTable . '.province_id', '=', $provinceTable . '.id');
            return DataTables::of($data)
                ->filterColumn('province_name', function ($qry, $keyword) use ($provinceTable) {
                    $qry->where($provinceTable . '.name', 'LIKE', '%' . $keyword . '%');
                })
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
                    if (auth()->guard('admin')->user()->hasPermission('District', 'edit')) {
                        $editUrl = route('admin.district.addEdit', ['id' => $row->id]);
                        $btn .= '<a href="' . $editUrl . '"><span
                        class="mat_sym-icon material-symbols-outlined"><i class="mdi mdi-pencil-outline"></i></span></a>';
                    }
                    if (auth()->guard('admin')->user()->hasPermission('District', 'delete')) {
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
    public function status($request) {
        try {
            District::where('id', $request->id)
                ->update([
                    $request->field_name => $request->val,
                ]);
            if ($request->val == 1) {
                $response['message'] = 'District enabled successfully';
            } else {
                $response['message'] = 'District disabled successfully';
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
            $district = $request['id'] ? District::firstOrNew(['id' => $request['id']]) : new District;
            $district->province_id = $request['province'];
            $district->name = $request['name'];
            $district->save();
            $response['message'] = ($request['id']) ? 'District updated successfully.' : 'District added successfully.';
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
            District::where('id', $id)->delete();
            $response['message'] = 'District deleted successfully.';
            $response['error'] = null;
            $response['status'] = 200;
            return response()->json($response, $response['status']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

}