<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create an Admin User
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'password' => bcrypt('password123'),
                'role' => 0, // Admin role
                'phone_number' => '09123456789',
                'address' => '123 Admin Street, City',
                'remember_token' => null,
                'email_verified_at' => now(),
            ]
        );

        // Create 20 Random Users using Factory
        User::factory()->count(10)->create();
    }
}
