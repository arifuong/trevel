<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with official Admin, Jamaah, Master Travel, and Gallery.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            JamaahResetSeeder::class,
            MasterTravelSeeder::class,
            GallerySeeder::class,
        ]);
    }
}
