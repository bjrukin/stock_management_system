<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        Admin::where('username', 'admin')->delete();
        $admin = new admin;
        $admin->name = 'Admin';
        $admin->email = 'info@nhpc.gov.np';
        $admin->username = 'admin';
        $admin->password = Hash::make('admin@2025');
        $admin->password_reference = 'admin@2025';
        $admin->role_id = 1;
        $admin->save();
    }
}



