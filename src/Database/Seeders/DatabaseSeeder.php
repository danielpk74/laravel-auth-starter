<?php

namespace Danielpk74\LaravelAuthStarter\Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run the user seeder
        $this->call([
            UserSeeder::class,
        ]);
    }
}
