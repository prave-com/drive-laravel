<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'email' => 'superadmin@example.com',
            'name' => 'Superadmin',
            'password' => Hash::make(env('APP_KEY')),
            'role' => UserRole::SUPERADMIN,
            'email_verified_at' => now(),
        ]);
    }
}
