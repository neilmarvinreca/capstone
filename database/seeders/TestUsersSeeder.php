<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'password' => Hash::make('password'),
                'role' => 'Super Admin',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Inventory Manager',
                'email' => 'inventory@example.com',
                'password' => Hash::make('password'),
                'role' => 'Inventory Manager',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Inspector',
                'email' => 'inspector@example.com',
                'password' => Hash::make('password'),
                'role' => 'Inspector',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Department User',
                'email' => 'user@example.com',
                'password' => Hash::make('password'),
                'role' => 'Department User',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
