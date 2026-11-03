<?php
namespace App\Services\Admin;

use App\Models\Program;
use App\Models\SubjectCommittee;
use Yajra\DataTables\Facades\DataTables;

class ProgramService {

    public function list() {
        try {
            $programTable = (new Program)->getTable();
            $subject_committeeTable = (new SubjectCommittee)->getTable();
            $data = Program::select($programTable . '.id', $programTable . '.name', $programTable . '.certificate_name', $programTable . '.status', $subject_committeeTable . '.name as subject_committee_name')->leftJoin($subject_committeeTable, $programTable . '.subject_committee_id', '=', $subject_committeeTable . '.id');
            return DataTables::of($data)
                ->filterColumn('subject_committee_name', function ($qry, $keyword) use ($subject_committeeTable) {
                    $qry->where($subject_committeeTable . '.name', 'LIKE', '%' . $keyword . '%');
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
                    if (auth()->guard('admin')->user()->hasPermission('Program', 'edit')) {
                        $editUrl = route('admin.program.addEdit', ['id' => $row->id]);
                        $btn .= '<a href="' . $editUrl . '"><span
                        class="mat_sym-icon material-symbols-outlined"><i class="mdi mdi-pencil-outline"></i></span></a>';
                    }
                    if (auth()->guard('admin')->user()->hasPermission('Program', 'delete')) {
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
            Program::where('id', $request->id)
                ->update([
                    $request->field_name => $request->val,
                ]);
            if ($request->val == 1) {
                $response['message'] = 'Program enabled successfully';
            } else {
                $response['message'] = 'Program disabled successfully';
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
            $program = $request['id'] ? Program::firstOrNew(['id' => $request['id']]) : new Program;
            $program->name = $request['name'];
            $program->certificate_name = $request['certificate_name'];
            $program->code = $request['code'];
            $program->qualification = $request['qualification'];
            $program->level_id = $request['level_id'];
            $program->subject_committee_id = $request['subject_committee_id'];
            $program->program_duration = $request['program_duration'];
            $program->duration_type = $request['duration_type'];
            $program->program_type = $request['program_type'];
            $program->has_exam = $request['has_exam'] ?? 0;
            $program->save();
            $response['message'] = ($request['id']) ? 'Program updated successfully.' : 'Program added successfully.';
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
            Program::where('id', $id)->delete();
            $response['message'] = 'Program deleted successfully.';
            $response['error'] = null;
            $response['status'] = 200;
            return response()->json($response, $response['status']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

}