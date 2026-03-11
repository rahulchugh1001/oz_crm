<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SfUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'SF001 User',
                'email' => 'sf001@ozone.com',
                'role' => 'SF001',
            ],
            [
                'name' => 'SF002 User',
                'email' => 'sf002@ozone.com',
                'role' => 'SF002',
            ],
            [
                'name' => 'SF003 User',
                'email' => 'sf003@ozone.com',
                'role' => 'SF003',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make('password'),
                    'role' => $user['role'],
                    'status' => true,
                    'is_deleted' => false,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
