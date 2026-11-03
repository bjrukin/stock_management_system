<?php

namespace App\Services\Admin;

use App\Exports\Admin\ApplicantExport;
use App\Jobs\Admin\GenerateAdmitCardJob;
use App\Models\ExamApply;
use App\Models\AdmitCard;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class AdmitCardService
{

    // function export($request)
    // {
    //     try {
    //         return Excel::download(new ApplicantExport($request), 'admitcard_list.xlsx');
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => $e->getMessage()], 400);
    //     }
    // }

    public function generateAdmitCard($request){
        // $exam_id = $request['exam_id'];
        // $program_id = $request['program_id'];
        // $subject_committee_id = $request['subject_committee_id'];
        // $level_id = $request['level_id'];
        $exam_id = $request->exam_id;
        $program_id = $request->program_id;
        $subject_committee_id = $request->subject_committee_id;
        $level_id = $request->level_id;
        $exam_date = $request->exam_date;
        $exam_time = $request->exam_time;
        $exam_center = $request->exam_center;
        $exam_instruction = $request->exam_instruction;
        $admit_card_base = 'subject_committee'; // $request->admit_card_base;
            
        $data = [
            'exam_id'=>$exam_id, 
            'program_id' => $program_id, 
            'subject_committee_id' => $subject_committee_id, 
            'level_id' => $level_id,  
            'user_id' => Auth::guard('admin')->id(),
            'exam_date' => $exam_date,
            'exam_time' => $exam_time,
            'exam_center' => $exam_center,
            'exam_instruction' => $exam_instruction,
            'admit_card_base' => $admit_card_base
        ];
        $jobToDispatch = (new GenerateAdmitCardJob($data))->delay(Carbon::now()->addSeconds(2));
        dispatch($jobToDispatch);
        return 'success';
    }
}