<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure the admin user exists
        DB::table('users')->updateOrInsert(
            ['email' => 'addministrator23@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('Wucce789#'), // Set a strong password
                'role_as' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
