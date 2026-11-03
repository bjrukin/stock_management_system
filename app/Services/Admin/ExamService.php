<?php
namespace App\Services\Admin;

use App\Models\AdmitCard;
use App\Models\Exam;
use Yajra\DataTables\Facades\DataTables;

class ExamService {

    public function list() {
        try {
            $data = Exam::select('*');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('admit_card_generated', function ($row) {
                    $checked = $row->admit_card_generated == 1 ? 'checked' : '';
                    $html = '<label class="switch">
                                <input type="checkbox" class="switch-input switch-admit_card_generated"
                                    data-id="' . $row->id . '" 
                                    data-admit_card_generated-value="' . $row->admit_card_generated . '" ' . $checked . '>
                                <span class="switch-label" data-on="Yes" data-off="No"></span>
                                <span class="switch-handle"></span>
                            </label>';
                    return $html;
                })
                ->addColumn('result_published', function ($row) {
                    $checked = $row->result_published == 1 ? 'checked' : '';
                    $html = '<label class="switch">
                                <input type="checkbox" class="switch-input switch-result_published"
                                    data-id="' . $row->id . '" 
                                    data-result_published-value="' . $row->result_published . '" ' . $checked . '>
                                <span class="switch-label" data-on="Yes" data-off="No"></span>
                                <span class="switch-handle"></span>
                            </label>';
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
                    if(auth()->guard('admin')->user()->hasPermission('Exam', 'edit')) {
                        $editUrl = route('admin.exam.addEdit', ['id' => $row->id]);
                        $btn .= '<a href="'.$editUrl.'"><span
                        class="mat_sym-icon material-symbols-outlined"><i class="mdi mdi-pencil-outline"></i></span></a>';
                    }
                    if(auth()->guard('admin')->user()->hasPermission('Exam', 'delete')) {
                        $btn .= ' <a href="javascript:void(0)" onclick="deleteData(' . $row->id . ')"><span class="mat_sym-icon material-symbols-outlined"><i class="mdi mdi-delete-forever-outline"></i></span></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['admit_card_generated', 'result_published', 'status', 'action'])
                ->make(true);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    public function status($request)
    {
        try {
            Exam::where('id', $request->id)
                ->update([
                    $request->field_name => $request->val,
                ]);
            if ($request->val == 1) {
                $response['message'] = 'Exam enabled successfully';
            } else {
                $response['message'] = 'Exam disabled successfully';
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
            $exam = $request['id'] ? Exam::firstOrNew(['id' => $request['id']]) : new Exam;
            $exam->exam_number = $request['exam_number'];
            $exam->name = $request['name'];
            $exam->name_nep = $request['name_nep'];
            $exam->opening_date = $request['opening_date'];
            $exam->closing_date = $request['closing_date'];
            $exam->exam_fee = $request['exam_fee'];
            $exam->late_fee_start_date = $request['late_fee_start_date'];
            $exam->late_fee = $request['late_fee'];
            $exam->description = $request['description'];
            $exam->save();
            $response['message'] = ($request['id']) ? 'Exam updated successfully.' : 'Exam added successfully.';
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
            Exam::where('id', $id)->delete();
            $response['message'] = 'Exam deleted successfully.';
            $response['error'] = null;
            $response['status'] = 200;
            return response()->json($response, $response['status']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function routineGenerate($request) {
        try {
            $student_number = $request->student_number??300;
            $shift_number = $request->shift_number??4;
            $exam_id = $request->id;
            $day = 1;
            $shift = 1;
            $data = array();
            $admitcards = AdmitCard::join('applicants', 'admit_cards.applicant_id', '=', 'applicants.id')
            ->join('programs', 'applicants.program_id', '=', 'programs.id')
            ->join('subject_committees', 'programs.subject_committee_id', '=', 'subject_committees.id')
            ->where('applicants.exam_id', $exam_id)
            ->orderBy('applicants.level_id', 'DESC')->orderBy('subject_committees.id')->orderBy('admit_cards.id')
            ->select('admit_cards.*') // Select only fields from admit_cards
            ->get();
            // dd($admitcards->tosql());
            // dd($admitcards->toArray());
            $admitcards->chunk($student_number)->each(function ($chunkedStudents, $index) use (&$day, &$shift, &$data, $shift_number) {
                // echo '<pre>';
                $first_admit_card = $chunkedStudents->first();
                $last_admit_card = $chunkedStudents->first();
                $subject_committee_id = '';
                $program_id = '';
                $level_id = '';
                $count = 0;
                
                // echo 'Day: ' . $day . ' : ' . 'Shift: ' . $shift. '<br>';
                foreach ($chunkedStudents as $student) {
                    // print_r($student['applicant']['program']->subject_committee_id);
                    if($count == 0){
                        $subject_committee_id = $student['applicant']['program']?->subject_committee_id;
                        $level_id = $student['applicant']->level_id;
                        $program_id = $student['applicant']['program']->id;
                        $count = 0;
                    }
                    // echo $program_id . '<br>';
                    if($subject_committee_id != $student['applicant']['program']->subject_committee_id || $level_id != $student['applicant']->level_id || $program_id != $student['applicant']['program']->id){
                        // echo $student['exam_apply']['program_id'] . '<br>';
                        if($first_admit_card['symbol_number'] != $last_admit_card['symbol_number']){
                            $data[$day][$shift][] =  array('program_id'=>$program_id, 'program'=>$first_admit_card['applicant']['program']->name, 'starting_symbol_number' => $first_admit_card['symbol_number'], 'ending_symbol_number' => $last_admit_card['symbol_number'], 'count' => $count);
                        }else{
                            $data[$day][$shift][] =  array('program_id'=>$program_id, 'program'=>$first_admit_card['applicant']['program']->name, 'starting_symbol_number' => $first_admit_card['symbol_number'], 'ending_symbol_number' => NULL, 'count' => $count);
                        }
                        $subject_committee_id = $student['applicant']['program']->subject_committee_id;
                        $program_id = $student['applicant']['program']->id;
                        $level_id = $student['applicant']->level_id;
                        $first_admit_card = $student;
                        $count = 0;
                    }
                    $count++;
                    $last_admit_card = $student;
                }
                if($first_admit_card['symbol_number'] != $last_admit_card['symbol_number']){
                    $data[$day][$shift][] =  array('program_id'=>$program_id, 'program'=>$first_admit_card['applicant']['program']->name, 'starting_symbol_number' => $first_admit_card['symbol_number'], 'ending_symbol_number' => $last_admit_card['symbol_number'], 'count' => $count);
                }else{
                    $data[$day][$shift][] =  array('program_id'=>$program_id, 'program'=>$first_admit_card['applicant']['program']->name, 'starting_symbol_number' => $first_admit_card['symbol_number'], 'ending_symbol_number' => NULL, 'count' => $count);
                }
                // echo '</pre>';
                if($shift == $shift_number){
                    $day++;
                    $shift=1;
                }else{
                    $shift++;
                }
            });
            return $data;
            
        } catch(\Exception $e) {
            return response()->json(['error'=> $e->getMessage()], 400);
        }
    }

}