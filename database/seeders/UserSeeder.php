<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // =====================
        // System Users (Roles)
        // =====================

        // Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@nca.com'],
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        if (method_exists($superAdmin, 'assignRole')) {
            $superAdmin->assignRole('Super Admin');
        }


        // Viewer
        $viewer = User::firstOrCreate(
            ['email' => 'viewer@nca.com'],
            [
                'first_name' => 'Viewer',
                'last_name' => 'User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        if (method_exists($viewer, 'assignRole')) {
            $viewer->assignRole('Viewer');
        }

        $this->command->info('Created system users: superadmin@nca.com, admin@nca.com, employee@nca.com, viewer@nca.com (password: password)');
    }
}
