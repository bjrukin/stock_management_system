<?php

namespace App\Console\Commands;

use App\Models\Professional;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportProfessional extends Command {
    protected $signature   = 'import:professional';
    protected $description = 'Import professional';

    public function handle() {
        $this->info('Processing...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Professional::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $uniqueIds = DB::table('users')
            ->select(DB::raw('MIN(id) as id'))
            ->where('role_id', 1)
            ->whereNotNull('email')
            ->groupBy('email');

        $users = DB::table('users')
            ->joinSub($uniqueIds, 'uniq', fn($j) => $j->on('users.id', '=', 'uniq.id'))
            ->select(
                'users.id',
                'users.name',
                'users.phone',
                'users.email',
                'users.password',
                'users.password_reference',
                'users.is_foreign',
                'users.status',
                'users.created_at',
                'users.updated_at'
            )
            ->orderBy('users.id')
            ->get();
        if ($users->isNotEmpty()) {
            foreach ($users as $user) {
                $this->info('Professional ID => ' . $user->id);
                $professional = Professional::firstOrNew(['id' => $user->id]);
                $professional->name = $user->name;
                $professional->phone = $user->phone;
                $professional->email = $user->email;
                $professional->password = $user->password;
                $professional->password_reference = $user->password_reference;
                $professional->is_foreign = $user->is_foreign;
                $professional->status = $user->status;
                $professional->created_at = $user->created_at;
                $professional->updated_at = $user->updated_at;
                $professional->save();
            }
        }
        $this->info('Professional Imported successfully.');
    }

}