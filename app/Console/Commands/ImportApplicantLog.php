<?php

namespace App\Console\Commands;

use App\Models\ApplicantLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportApplicantLog extends Command {
    protected $signature   = 'import:applicantlog';
    protected $description = 'Import applicant log';

    public function handle() {
        $this->info('Processing...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ApplicantLog::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $exam_logs = DB::table('exam_logs')
            ->select('exam_logs.*')
            ->join('applicants', 'applicants.id', '=', 'exam_logs.exam_apply_id')
            ->join('professionals', 'professionals.id', '=', 'exam_logs.user_id')
            ->orderBy('id')
            ->get();
        if ($exam_logs->isNotEmpty()) {
            foreach ($exam_logs as $exam_log) {
                $this->info('Applicant Log ID => ' . $exam_log->id);
                $applicant_log = ApplicantLog::firstOrNew(['id' => $exam_log->id]);
                $applicant_log->professional_id = $exam_log->user_id;
                $applicant_log->admin_id = $exam_log->system_user_id;
                $applicant_log->applicant_id = $exam_log->exam_apply_id;
                $applicant_log->remarks = $exam_log->remarks;
                $applicant_log->state = $exam_log->state;
                $applicant_log->status = $exam_log->status;
                $applicant_log->created_at = $exam_log->created_at;
                $applicant_log->updated_at = $applicant_log->updated_at;
                $applicant_log->save();
            }
            $this->info('Applicant Log Imported successfully.');
        }

    }

}