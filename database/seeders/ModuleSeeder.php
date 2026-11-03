<?php

namespace Database\Seeders;

use App\Models\Module;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ModuleSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        Schema::disableForeignKeyConstraints();
        Module::truncate();
        Schema::enableForeignKeyConstraints();

        $data = [
                ['name' => 'Module', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], 
                ['name' => 'Role', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'Permission', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'User', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ];
        Module::insert($data);
    }
}