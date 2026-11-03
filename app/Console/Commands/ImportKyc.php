<?php

namespace App\Console\Commands;

use App\Models\Kyc;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportKyc extends Command {
    protected $signature   = 'import:kyc';
    protected $description = 'Import kyc';

    public function handle() {
        $this->info('Processing...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Kyc::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $kycs = DB::table('kycs1')
            ->select('kycs1.*')
            ->join('professionals', 'professionals.id', '=', 'kycs1.user_id')
            ->orderBy('id')
            ->get();
        if ($kycs->isNotEmpty()) {
            foreach ($kycs as $kyc) {
                $this->info('Kyc ID => ' . $kyc->id);
                $data = Kyc::firstOrNew(['id' => $kyc->id]);
                $data->professional_id = $kyc->user_id;
                $data->name = $kyc->name;
                $data->dob = $kyc->dob;
                $data->registration_number = $kyc->registration_number;
                $data->symbol_number = $kyc->symbol_number;
                $data->profile_img = $kyc->profile_img;
                $data->created_at = $kyc->created_at;
                $data->updated_at = $kyc->updated_at;
                $data->save();
            }
        }
        $this->info('Kyc Imported successfully.');
    }

}