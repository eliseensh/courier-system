<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'], // change if you want
            [
                'name' => 'System Admin',
                'password' => Hash::make('password123'), // change password
                'role' => 'admin',
            ]
        );
    }
}
