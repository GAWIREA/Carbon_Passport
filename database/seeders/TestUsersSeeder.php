<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Enums\UserRole;

class TestUsersSeeder extends Seeder
{
    public function run()
    {
        for ($i = 1; $i <= 10; $i++) {
            User::firstOrCreate(
                ['email' => "testuser{$i}@example.com"],
                [
                    'name' => "Test User {$i}",
                    'username' => "testuser{$i}",
                    'password' => Hash::make('password'),
                    'role' => UserRole::User,
                    'monthly_points' => 0,
                    'coins' => 0,
                    'xp' => 0,
                    'level' => 1
                ]
            );
        }
    }
}
