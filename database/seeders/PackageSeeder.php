<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Package seeder dikosongkan agar tidak ada paket dummy di database.
     * Paket dibuat secara mandiri oleh admin via menu CRUD /admin/packages.
     */
    public function run(): void
    {
        // Kosong sesuai instruksi cleanup data dummy
    }
}
