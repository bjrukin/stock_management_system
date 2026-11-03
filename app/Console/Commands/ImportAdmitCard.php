<?php

namespace App\Console\Commands;

use App\Models\AdmitCard;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportAdmitCard extends Command {
    protected $signature   = 'import:admitcard';
    protected $description = 'Import admit card';

    public function handle() {
        $this->info('Processing...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('admit_cards1')->where('created_by', 20)->update(['created_by' => 5383, 'updated_by' => 5383]);
        AdmitCard::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $admit_cards = DB::table('admit_cards1')
            ->select('admit_cards1.*')
            ->join('professionals', 'professionals.id', '=', 'admit_cards1.user_id')
            ->join('applicants', 'applicants.id', '=', 'admit_cards1.exam_apply_id')
            ->orderBy('id')
            ->get();
        if ($admit_cards->isNotEmpty()) {
            foreach ($admit_cards as $admit_card) {
                $this->info('Admit Card ID => ' . $admit_card->id);
                $card = AdmitCard::firstOrNew(['id' => $admit_card->id]);
                $card->professional_id = $admit_card->user_id;
                $card->applicant_id = $admit_card->exam_apply_id;
                $card->symbol_number = $admit_card->symbol_number;
                $card->is_taken = $admit_card->is_taken;
                $card->created_by = $admit_card->created_by;
                $card->updated_by = $admit_card->updated_by;
                $card->created_at = $admit_card->created_at;
                $card->updated_at = $admit_card->updated_at;
                $card->save();
            }
        }
        $this->info('Admin Card Imported successfully.');
    }

}