<?php
namespace App\Services\Admin;

use App\Models\SubjectCommitteeUser;
use Yajra\DataTables\Facades\DataTables;

class SubjectCommitteeUserService {

    public function list() {
        try {
            $data = SubjectCommitteeUser::select('*')->orderBy('subject_committee_id', 'asc')->orderBy('coordinator', 'desc');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('user_name', function ($row) {
                    $user = $row->user;
                    return $user->name ?? '-';
                })
                ->addColumn('subject_committee_name', function ($row) {
                    $subject_committee = $row->subject_committee;
                    return $subject_committee->name ?? '-';
                })
                ->addColumn('coordinator', function ($row) {
                    $checked = $row->coordinator == 1 ? 'checked' : '';
                    $html = '<label class="switch">
                                <input type="checkbox" class="switch-input switch-coordinator"
                                    data-id="' . $row->id . '"   data-subject_committee_id="' . $row->subject_committee_id . '"
                                    data-coordinator-value="' . $row->coordinator . '" ' . $checked . '>
                                <span class="switch-label" data-on="Yes" data-off="No"></span>
                                <span class="switch-handle"></span>
                            </label>';
                    return $html;
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    if (auth()->guard('admin')->user()->hasPermission('Subject Committee User', 'edit')) {
                        $editUrl = route('admin.subjectcommitteeuser.addEdit', ['id' => $row->id]);
                        $btn .= '<a href="' . $editUrl . '"><span
                        class="mat_sym-icon material-symbols-outlined"><i class="mdi mdi-pencil-outline"></i></span></a>';
                    }
                    if (auth()->guard('admin')->user()->hasPermission('SubjectCommitteeUser', 'delete')) {
                        $btn .= ' <a href="javascript:void(0)" onclick="deleteData(' . $row->id . ')"><span class="mat_sym-icon material-symbols-outlined"><i class="mdi mdi-delete-forever-outline"></i></span></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['coordinator', 'status', 'action'])
                ->make(true);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    public function status($request) {
        try {
            SubjectCommitteeUser::where('subject_committee_id', $request->subject_committee_id)
                ->update([
                    $request->field_name => 0,
                ]);
            SubjectCommitteeUser::where('id', $request->id)
                ->update([
                    $request->field_name => $request->val,
                ]);
            $active = SubjectCommitteeUser::where(['subject_committee_id' => $request->subject_committee_id, 'coordinator' => 1])->first();
            if (! $active) {
                $fallback = SubjectCommitteeUser::where('subject_committee_id', $request->subject_committee_id)
                    ->where('id', '<>', $request->id)
                    ->first();
                if ($fallback) {
                    $fallback->update([$request->field_name => 1]);
                }
            }

            if ($request->val == 1) {
                $response['message'] = 'Subject Committee User enabled successfully';
            } else {
                $response['message'] = 'Subject Committee User disabled successfully';
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
            $subjectcommitteeuser = $request['id'] ? SubjectCommitteeUser::firstOrNew(['id' => $request['id']]) : new SubjectCommitteeUser;
            $subjectcommitteeuser->user_id = $request['user'];
            $subjectcommitteeuser->subject_committee_id = $request['subject_committee'];
            $subjectcommitteeuser->save();
            $response['message'] = ($request['id']) ? 'SubjectCommitteeUser updated successfully.' : 'Subject Committee User added successfully.';
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
            SubjectCommitteeUser::where('id', $id)->delete();
            $response['message'] = 'Subject Committee User deleted successfully.';
            $response['error'] = null;
            $response['status'] = 200;
            return response()->json($response, $response['status']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

}