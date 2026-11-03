<?php
namespace App\Services\Admin;

use App\Models\EligibilityMark;
use Yajra\DataTables\Facades\DataTables;

class EligibilityMarkService {

    public function list() {
        try {

            $data = EligibilityMark::with('level:id,name')->select('*');
            return DataTables::of($data)
                ->filterColumn('level_name', function ($query, $keyword) {
                    $query->whereHas('level', function ($qry) use ($keyword) {
                        $qry->where('name', 'LIKE', '%' . $keyword . '%');
                    });
                })
                ->addIndexColumn()
                ->addColumn('level_name', function ($row) {
                    return $row->level->name ?? null;
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    if (auth()->guard('admin')->user()->hasPermission('EligibilityMark', 'edit')) {
                        $editUrl = route('admin.eligibilitymark.addEdit', ['id' => $row->id]);
                        $btn .= '<a href="' . $editUrl . '"><span
                        class="mat_sym-icon material-symbols-outlined"><i class="mdi mdi-pencil-outline"></i></span></a>';
                    }
                    if (auth()->guard('admin')->user()->hasPermission('EligibilityMark', 'delete')) {
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
    public function store($request) {
        try {
            $eligibilitymark = $request['id'] ? EligibilityMark::firstOrNew(['id' => $request['id']]) : new EligibilityMark;
            $eligibilitymark->level_id = $request['level_id'];
            $eligibilitymark->min_percentage = $request['min_percentage'];
            $eligibilitymark->min_gpa = $request['min_gpa'];
            $eligibilitymark->min_physics_marks = $request['min_physics_marks'];
            $eligibilitymark->min_chemistry_marks = $request['min_chemistry_marks'];
            $eligibilitymark->min_bio_marks = $request['min_bio_marks'];
            $eligibilitymark->total_days_passport = $request['total_days_passport'];
            $eligibilitymark->total_days_internship = $request['total_days_internship'];
            $eligibilitymark->save();
            $response['message'] = ($request['id']) ? 'Eligibility Mark updated successfully.' : 'Eligibility Mark added successfully.';
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
            EligibilityMark::where('id', $id)->delete();
            $response['message'] = 'EligibilityMark deleted successfully.';
            $response['error'] = null;
            $response['status'] = 200;
            return response()->json($response, $response['status']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

}