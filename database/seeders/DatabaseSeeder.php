<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with official Admin and 1 clean Jamaah account.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            JamaahResetSeeder::class,
        ]);
    }
}

