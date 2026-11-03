<?php

namespace App\Console\Commands;

use App\Models\Applicant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportApplicant extends Command {
    protected $signature   = 'import:applicant';
    protected $description = 'Import applicant';

    public function handle() {
        $this->info('Processing...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Applicant::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $exam_applies = DB::table('exam_applies')
            ->select('exam_applies.*')
            ->join('professionals', 'professionals.id', '=', 'exam_applies.user_id')
            ->orderBy('id')
            ->get();
        if ($exam_applies->isNotEmpty()) {
            foreach ($exam_applies as $exam_apply) {
                $this->info('Applicant ID => ' . $exam_apply->id);
                $applicant = Applicant::firstOrNew(['id' => $exam_apply->id]);
                $applicant->exam_id = $exam_apply->exam_id;
                $applicant->professional_id = $exam_apply->user_id;
                $applicant->voucher_image = $exam_apply->voucher_image;
                $applicant->remarks = $exam_apply->remarks;
                $applicant->subject_committee_count = $exam_apply->subject_committee_count;
                $applicant->level_id = $exam_apply->level_id;
                $applicant->program_id = $exam_apply->program_id;
                $applicant->attempt = $exam_apply->attempt ?? 1;
                $applicant->is_admit_card_generate = $exam_apply->is_admit_card_generate;
                $applicant->is_certificate_generate = $exam_apply->is_certificate_generate;
                $applicant->is_passed = $exam_apply->is_passed;
                $applicant->rejected = $exam_apply->rejected;
                $applicant->state = $exam_apply->state;
                $applicant->status = $exam_apply->status;
                $applicant->created_at = $exam_apply->created_at;
                $applicant->updated_at = $exam_apply->updated_at;
                $applicant->save();
            }
        }

        $this->info('Applicant Imported successfully.');
    }

}