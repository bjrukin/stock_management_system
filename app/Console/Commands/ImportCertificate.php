<?php

namespace App\Console\Commands;

use App\Models\Certificate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportCertificate extends Command {
    protected $signature   = 'import:certificate';
    protected $description = 'Import certificate';

    public function handle() {
        $this->info('Processing...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Certificate::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $certificates = DB::table('certificates1')
            ->select('certificates1.*')
            ->join('professionals', 'professionals.id', '=', 'certificates1.user_id')
            ->orderBy('id')
            ->get();
        if ($certificates->isNotEmpty()) {
            foreach ($certificates as $certificate) {
                $this->info('Certificate ID => ' . $certificate->id);
                $data = Certificate::firstOrNew(['id' => $certificate->id]);
                $data->professional_id = $certificate->user_id;
                $data->printed_by = $certificate->printed_by;
                $data->issued_by = $certificate->issued_by;
                $data->level_id = $certificate->level_id;
                $data->program_id = $certificate->program_id;
                $data->registration_id = $certificate->registration_id;
                $data->srn = $certificate->srn;
                $data->program_certificate_code = $certificate->program_certificate_code;
                $data->cert_registration_number = $certificate->cert_registration_number;
                $data->registrar = $certificate->registrar;
                $data->name = $certificate->name;
                $data->date_of_birth = $certificate->date_of_birth;
                $data->address = $certificate->address;
                $data->qualification = $certificate->qualification;
                $data->decision_date = $certificate->decision_date;
                $data->issued_year = $certificate->issued_year;
                $data->issued_date = $certificate->issued_date;
                $data->valid_till = $certificate->valid_till;
                $data->certificate = $certificate->certificate;
                $data->type = $certificate->type;
                $data->remarks = $certificate->remarks;
                $data->is_printed = $certificate->is_printed;
                $data->printed_date = $certificate->printed_date;
                $data->is_edited = $certificate->is_edited;
                $data->status = $certificate->certificate_status == 'active' ? 1 : 0;
                $data->created_at = $certificate->created_at;
                $data->updated_at = $certificate->updated_at;
                $data->save();
            }
        }
        $this->info('Certificate Imported successfully.');
    }

}