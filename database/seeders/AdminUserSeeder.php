<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    
    public function run(): void
        {
            // Optional: clear only admin users (safe way)
            User::whereIn('email', [
                'super.admin@ozone.com',
                'admin@ozone.com'
            ])->delete();

            $users = [
                [
                    'name' => 'Super Admin',
                    'email' => 'super.admin@ozone.com',
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ],
                [
                    'name' => 'Admin',
                    'email' => 'admin@ozone.com',
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ],
            ];

            foreach ($users as $user) {
                User::create($user);
            }
        }
    


}
