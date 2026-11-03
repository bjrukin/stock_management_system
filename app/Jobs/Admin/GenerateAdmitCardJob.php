<?php

namespace App\Jobs\Admin;

use App\Models\AdmitCard;
use App\Models\Applicant;
use App\Models\Exam;
use App\Models\Level;
use App\Models\Program;
use App\Models\SubjectCommittee;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateAdmitCardJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $data;

    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $exam_id = $this->data['exam_id'];
        $program_id = $this->data['program_id'];
        $subject_committee_id = $this->data['subject_committee_id'];
        $level_id = $this->data['level_id'];
        $exam_date = $this->data['exam_date'];
        $exam_time = $this->data['exam_time'];
        $exam_center = $this->data['exam_center'];
        $exam_instruction = $this->data['exam_instruction'];
        $admit_card_base = $this->data['admit_card_base'];
        $exam = Exam::where('id', $exam_id)->first();
        
        $subject_committees = $this->getSubjectCommitteeList($subject_committee_id);

        $levels = $this->getLevelList($level_id);
        $symbol_number_format = '{{ exam_number }}-{{ level_code }}{{ roll_number }}-{{ subject_committee_code }}';

        foreach($levels as $level) {
            $roll_number = 1;
            foreach($subject_committees as $subject_committee){
                if($admit_card_base == 'subject_committee') {
                    $roll_number = 1;
                }
                $programs = $this->getProgramList($subject_committee->id, $level->id, $program_id);
                foreach($programs as $program){
                    if($admit_card_base == 'program') {
                        $roll_number = 1;
                    }
                    $applicants = $this->getApplicantList($exam_id, $program->id, $level->id);
                    foreach($applicants as $applicant) {
                        $admitcard = new AdmitCard();
                        $admitcard->symbol_number = str_replace(
                            ['{{ exam_number }}', '{{ level_code }}', '{{ roll_number }}', '{{ subject_committee_code }}', '{{ program_code }}'],
                            [
                                $exam->exam_number,
                                $level->code,
                                $roll_number,
                                $subject_committee->code,
                                $program->code
                            ],
                            $symbol_number_format
                        );
                        $admitcard->professional_id = $applicant->professional_id;
                        $admitcard->applicant_id = $applicant->id;
                        $admitcard->exam_date = $exam_date;
                        $admitcard->exam_time = $exam_time;
                        $admitcard->exam_center = $exam_center;
                        $admitcard->exam_instruction = $exam_instruction;
                        $admitcard->save();

                        $applicant->is_admit_card_generate = 1;
                        $applicant->save();
                        $roll_number++;
                    }

                }
            }
        }
    }

    private function getSubjectCommitteeList($subject_committee_id) {
        $subject_committee_query = SubjectCommittee::where('status', 1);
        if($subject_committee_id) {
            $subject_committee_query = $subject_committee_query->where('id', $subject_committee_id);
        }
        return $subject_committee_query->get();
    }

    private function getLevelList($level_id) {
        $level_query = Level::where('status', 1);
        if($level_id) {
            $level_query = $level_query->where('id', $level_id);
        }
        return $level_query->get();
    }

    private function getProgramList($subject_committee_id, $level_id, $program_id) {
        $program_query = Program::where('status', 1)->where('subject_committee_id', $subject_committee_id)->where('level_id', $level_id);
        if($program_id){
            $program_query = $program_query->where('id', $program_id);
        }
        return $program_query->get();
    }

    private function getApplicantList($exam_id, $program_id, $level_id) {
        $applicant_query = Applicant::where([
            'state' => 'exam_committee',
            'exam_id' => $exam_id,
            'program_id' => $program_id,
        ]);
        if($level_id) {
            $applicant_query = $applicant_query->where('level_id', $level_id);
        }
        return $applicant_query->get();
    }
}
