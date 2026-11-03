<?php
namespace App\Services\Admin;

use App\Models\VerificationStep;
use App\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class VerificationStepService {

    public function list() {
        try {
            $verificationStepTable = (new VerificationStep)->getTable();
            $roleTable = (new Role)->getTable();
            
            $data = VerificationStep::select($verificationStepTable . '.id', $verificationStepTable . '.step', $roleTable . '.name as role_name')->leftJoin($roleTable, $verificationStepTable . '.role_id', '=', $roleTable . '.id')->orderBy($verificationStepTable . '.step', 'asc');
            return DataTables::of($data)
                ->filterColumn('role_name', function ($qry, $keyword) use ($roleTable) {
                    $qry->where($roleTable . '.name', 'LIKE', '%' . $keyword . '%');
                })
                ->addIndexColumn()
                ->addColumn('step', function ($row) use ($data) {
                    $total = $data->count();
                    $html = '<select name="step" class="form-select" onchange="changeOrder(' . $row->id . ', this)">';
                    for ($i = 1; $i <= $total; $i++) {
                        $selected = ($row->step == $i) ? 'selected' : '';
                        $html .= '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
                    }
                    $html .= '</select>';
                    return $html;
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    if (auth()->guard('admin')->user()->hasPermission('VerificationStep', 'edit')) {
                        $editUrl = route('admin.verificationstep.addEdit', ['id' => $row->id]);
                        $btn .= '<a href="' . $editUrl . '"><span
                        class="mat_sym-icon material-symbols-outlined"><i class="mdi mdi-pencil-outline"></i></span></a>';
                    }
                    if (auth()->guard('admin')->user()->hasPermission('VerificationStep', 'delete')) {
                        $btn .= ' <a href="javascript:void(0)" onclick="deleteData(' . $row->id . ')"><span class="mat_sym-icon material-symbols-outlined"><i class="mdi mdi-delete-forever-outline"></i></span></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['step', 'action'])
                ->make(true);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function step($request) {
        try {
            VerificationStep::where('id', $request->id)->update(['step' => $request->step]);
            $response['message'] = 'Step order updated successfully';
            $response['error'] = null;
            $response['status'] = 200;
            return response()->json($response, $response['status']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    public function status($request) {
        try {
            VerificationStep::where('id', $request->id)
                ->update([
                    $request->field_name => $request->val,
                ]);
            if ($request->val == 1) {
                $response['message'] = 'Verification step enabled successfully';
            } else {
                $response['message'] = 'Verification step disabled successfully';
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
            $verificationstep = $request['id'] ? VerificationStep::firstOrNew(['id' => $request['id']]) : new VerificationStep;
            $verificationstep->role_id = $request['role'];
            $verificationstep->step = $request['id'] ? $verificationstep->step : getMax('verification_steps', 'step');
            $verificationstep->save();
            $response['message'] = ($request['id']) ? 'Verification step updated successfully.' : 'Verification step added successfully.';
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
            VerificationStep::where('id', $id)->delete();
            $response['message'] = 'Verification step deleted successfully.';
            $response['error'] = null;
            $response['status'] = 200;
            return response()->json($response, $response['status']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

}