<?php

namespace Danielpk74\LaravelAuthStarter\Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userModel = config('auth-starter.models.user', \App\Models\User::class);
        
        // Create admin user
        $userModel::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 1, // Admin role
            'email_verified_at' => now(),
        ]);
        
        // Create regular user
        $userModel::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'role' => 2, // User role
            'email_verified_at' => now(),
        ]);
        
        // Create additional test users if factories exist
        if (method_exists($userModel, 'factory')) {
            $userModel::factory()->count(8)->create();
        }
    }
}
