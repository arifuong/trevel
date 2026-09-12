<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class JamaahDemoSeeder extends Seeder
{
    /**
     * Buat tepat 1 akun jamaah demo resmi untuk kebutuhan testing/demo.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'jamaah.demo@zeintour.test'],
            [
                'name' => 'Jamaah Demo',
                'phone' => '6281234567890',
                'password' => Hash::make('password123'),
                'role' => 'jamaah',
            ]
        );
    }
}
