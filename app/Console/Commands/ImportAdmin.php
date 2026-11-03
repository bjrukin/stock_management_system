<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportAdmin extends Command {
    protected $signature   = 'import:systemuser';
    protected $description = 'Import system user';

    public function handle() {
        $this->info('Processing...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Admin::where('id', '<>', 1)->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        DB::table('users')
            ->where('role_id', '<>', 1)
            ->where('id', '<>', 1)
            ->orderBy('id')
            ->chunkById(1000, function ($users) {
                $admins = [];
                foreach ($users as $user) {
                    $this->info('Admin ID => ' . $user->id);
                    $admins[] = [
                        'id'                 => $user->id,
                        'role_id'            => ($user->role_id == 8) ? 2 : ($user->role_id + 1),
                        'name'               => $user->name,
                        'phone'              => $user->phone,
                        'email'              => $user->email,
                        'username'           => $user->email,
                        'password'           => $user->password,
                        'password_reference' => $user->password_reference,
                        'created_at'         => now(),
                        'updated_at'         => now(),
                    ];
                }
                Admin::insert($admins);
            });

        $this->info('Admin Imported successfully.');
    }

}