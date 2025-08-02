<?php

namespace Danielpk74\LaravelAuthStarter\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Danielpk74\LaravelAuthStarter\Enums\RoleType;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userModel = config('auth-starter.models.user', \App\Models\User::class);
        
        // Create default admin user
        $userModel::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => RoleType::Admin->value,
            ]
        );

        // Create default regular user
        $userModel::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Regular User',
                'password' => Hash::make('password'),
                'role' => RoleType::User->value,
            ]
        );

        // Create additional test users if factory exists
        if (class_exists(\Database\Factories\UserFactory::class)) {
            $userModel::factory()->count(10)->create();
        }
    }
}
