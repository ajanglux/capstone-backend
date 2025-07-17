<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'password' => bcrypt('password123'),
                'role' => 1,
                'phone_number' => '09123456789',
                'address' => '123 Admin Street, City',
                'remember_token' => null,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'aj@gmail.com'],
            [
                'first_name' => 'Allyssa',
                'last_name' => 'User',
                'password' => bcrypt('password123'),
                'role' => 0,
                'phone_number' => '09123456781',
                'address' => '123 Admin Street, City',
                'remember_token' => null,
                'email_verified_at' => now(),
            ]
        );
    }
}
