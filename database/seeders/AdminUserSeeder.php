<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // SUPERADMIN
        User::firstOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('superadmin'),
                'role' => 'superadmin',
            ]
        );

        // ADMIN BIASA (optional)
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin Biasa',
                'password' => Hash::make('admin'),
                'role' => 'admin',
            ]
        );
    }
}
