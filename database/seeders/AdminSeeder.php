<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Buat akun admin default untuk testing.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@zeintour.com'],
            [
                'name' => 'Admin Zein Tour',
                'phone' => '6282121483337',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );
    }
}
