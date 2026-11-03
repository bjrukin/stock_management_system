<?php

namespace App\Console\Commands;

use App\Models\ProfessionalInfo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportProfessionalInfo extends Command {
    protected $signature   = 'import:professionalinfo';
    protected $description = 'Import professional info';

    public function handle() {
        $this->info('Processing...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ProfessionalInfo::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        DB::table('user_infos')->where('program_id', 0)->update(['program_id' => null]);
        DB::table('user_infos')->where('district_id', 0)->update(['district_id' => 48]);

        $uniqueIds = DB::table('user_infos')
            ->select(DB::raw('MIN(id) as id'))
            ->whereNotNull('user_id')
            ->groupBy('user_id');

        $user_infos = DB::table('user_infos')
            ->select('user_infos.*')
            ->joinSub($uniqueIds, 'uniq', fn($j) => $j->on('user_infos.id', '=', 'uniq.id'))
            ->join('professionals', 'professionals.id', '=', 'user_infos.user_id')
            ->orderBy('user_infos.id')
            ->get();

        if ($user_infos->isNotEmpty()) {
            foreach ($user_infos as $user_info) {
                $this->info('Professional Info ID => ' . $user_info->id);
                $professional_info = ProfessionalInfo::firstOrNew(['id' => $user_info->id]);
                $professional_info->professional_id = $user_info->user_id;
                $professional_info->program_id = $user_info->program_id;
                $professional_info->level_id = $user_info->level_id;
                $professional_info->first_name = $user_info->first_name;
                $professional_info->middle_name = $user_info->middle_name;
                $professional_info->last_name = $user_info->last_name;
                $professional_info->first_name_nep = $user_info->first_name_nep;
                $professional_info->middle_name_nep = $user_info->middle_name_nep;
                $professional_info->last_name_nep = $user_info->last_name_nep;
                $professional_info->dob_eng = $user_info->dob_eng;
                $professional_info->dob_nep = $user_info->dob_nep;
                $professional_info->gender = $user_info->gender;
                $professional_info->marital_status = $user_info->marital_status;
                $professional_info->ethinic = $user_info->ethinic;
                $professional_info->citizenship_number = $user_info->citizenship_number;
                $professional_info->citizenship_issue_date = $user_info->citizenship_issue_date;
                $professional_info->citizenship_issue_district = $user_info->citizenship_issue_district;
                $professional_info->father_name = $user_info->father_name;
                $professional_info->father_name_nep = $user_info->father_name_nep;
                $professional_info->grandfather_name = $user_info->grandfather_name;
                $professional_info->grandfather_name_nep = $user_info->grandfather_name_nep;
                $professional_info->mother_name = $user_info->mother_name;
                $professional_info->mother_name_nep = $user_info->mother_name_nep;
                $professional_info->province_id = $user_info->province_id;
                $professional_info->district_id = $user_info->district_id;
                $professional_info->municipality_id = $user_info->municipality_id;
                $professional_info->ward_no = $user_info->ward_no;
                $professional_info->profile_picture = $user_info->profile_picture;
                $professional_info->citizenship_front = $user_info->citizenship_front;
                $professional_info->citizenship_back = $user_info->citizenship_back;
                $professional_info->signature_image = $user_info->signature_image;
                $professional_info->profile_state = $user_info->profile_state;
                $professional_info->created_at = $user_info->created_at;
                $professional_info->updated_at = $user_info->updated_at;
                $professional_info->save();
            }
        }
        $this->info('Professional Info Imported successfully.');
    }

}