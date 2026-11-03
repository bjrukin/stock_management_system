<?php
namespace App\Services\Admin;

use App\Models\District;
use App\Models\Municipality;
use Yajra\DataTables\Facades\DataTables;

class MunicipalityService {

    public function list() {
        try {
            $municipalityTable = (new Municipality)->getTable();
            $districtTable = (new District)->getTable();
            $data = Municipality::select($municipalityTable . '.id', $municipalityTable . '.name', $municipalityTable . '.district_id', $municipalityTable . '.status', $districtTable . '.name as district_name')->leftJoin($districtTable, $municipalityTable . '.district_id', '=', $districtTable . '.id');
            return DataTables::of($data)
                ->filterColumn('district_name', function ($qry, $keyword) use ($districtTable) {
                    $qry->where($districtTable . '.name', 'LIKE', '%' . $keyword . '%');
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
                    if (auth()->guard('admin')->user()->hasPermission('Municipality', 'edit')) {
                        $editUrl = route('admin.municipality.addEdit', ['id' => $row->id]);
                        $btn .= '<a href="' . $editUrl . '"><span
                        class="mat_sym-icon material-symbols-outlined"><i class="mdi mdi-pencil-outline"></i></span></a>';
                    }
                    if (auth()->guard('admin')->user()->hasPermission('Municipality', 'delete')) {
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
            Municipality::where('id', $request->id)
                ->update([
                    $request->field_name => $request->val,
                ]);
            if ($request->val == 1) {
                $response['message'] = 'Municipality enabled successfully';
            } else {
                $response['message'] = 'Municipality disabled successfully';
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
            $municipality = $request['id'] ? Municipality::firstOrNew(['id' => $request['id']]) : new Municipality;
            $municipality->district_id = $request['district'];
            $municipality->name = $request['name'];
            $municipality->save();
            $response['message'] = ($request['id']) ? 'Municipality updated successfully.' : 'Municipality added successfully.';
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
            Municipality::where('id', $id)->delete();
            $response['message'] = 'Municipality deleted successfully.';
            $response['error'] = null;
            $response['status'] = 200;
            return response()->json($response, $response['status']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

}