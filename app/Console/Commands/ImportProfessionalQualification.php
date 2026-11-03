<?php

namespace App\Console\Commands;

use App\Models\ProfessionalQualification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportProfessionalQualification extends Command {
    protected $signature   = 'import:professionalqualification';
    protected $description = 'Import professional qualification';

    public function handle() {
        $this->info('Processing...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ProfessionalQualification::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $user_qualifications = DB::table('user_qualifications')
            ->select('user_qualifications.*')
            ->join('professionals', 'professionals.id', '=', 'user_qualifications.user_id')
            ->orderBy('id')
            ->get();
        if ($user_qualifications->isNotEmpty()) {
            foreach ($user_qualifications as $user_qualification) {
                $this->info('Professional Qualification ID => ' . $user_qualification->id);
                $professional_qualification = ProfessionalQualification::firstOrNew(['id' => $user_qualification->id]);
                $professional_qualification->professional_id = $user_qualification->user_id;
                $professional_qualification->level_id = $user_qualification->level_id;
                $professional_qualification->name = $user_qualification->name;
                $professional_qualification->board_university = $user_qualification->board_university;
                $professional_qualification->passed_year = $user_qualification->passed_year;
                $professional_qualification->admission_year = $user_qualification->admission_year;
                $professional_qualification->college_name = $user_qualification->college_name;
                $professional_qualification->registration_number = $user_qualification->registration_number;
                $professional_qualification->transcript_image = $user_qualification->transcript_image;
                $professional_qualification->provisional_image = $user_qualification->provisional_image;
                $professional_qualification->character_image = $user_qualification->character_image;
                $professional_qualification->ojt_image = $user_qualification->ojt_image;
                $professional_qualification->visa_image = $user_qualification->visa_image;
                $professional_qualification->noc_image = $user_qualification->noc_image;
                $professional_qualification->intership_image = $user_qualification->intership_image;
                $professional_qualification->passport_image = $user_qualification->passport_image;
                $professional_qualification->licence = $user_qualification->licence;
                $professional_qualification->council_registration_certificate = $user_qualification->council_registration_certificate;
                $professional_qualification->ojt_pcl_community_1_image = $user_qualification->ojt_pcl_community_1_image;
                $professional_qualification->ojt_pcl_community_2_image = $user_qualification->ojt_pcl_community_2_image;
                $professional_qualification->transcript_mas_marksheet = $user_qualification->transcript_mas_marksheet;
                $professional_qualification->transcript_bac_1 = $user_qualification->transcript_bac_1;
                $professional_qualification->transcript_bac_2 = $user_qualification->transcript_bac_2;
                $professional_qualification->transcript_bac_3 = $user_qualification->transcript_bac_3;
                $professional_qualification->transcript_bac_4 = $user_qualification->transcript_bac_4;
                $professional_qualification->transcript_bac_5 = $user_qualification->transcript_bac_5;
                $professional_qualification->transcript_bac_6 = $user_qualification->transcript_bac_6;
                $professional_qualification->transcript_bac_7 = $user_qualification->transcript_bac_7;
                $professional_qualification->transcript_bac_8 = $user_qualification->transcript_bac_8;
                $professional_qualification->migration_image = $user_qualification->migration_image;
                $professional_qualification->equivalence_certificate = $user_qualification->equivalence_certificate;
                $professional_qualification->created_at = $user_qualification->created_at;
                $professional_qualification->updated_at = $user_qualification->updated_at;
                $professional_qualification->save();
            }
        }

        $this->info('Professional Qualification Imported successfully.');
    }

}