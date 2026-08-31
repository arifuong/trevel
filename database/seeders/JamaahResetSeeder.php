<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class JamaahResetSeeder extends Seeder
{
    /**
     * Reset seluruh akun Jamaah dan buat tepat 1 akun Jamaah baru.
     *
     * Data Akun Baru:
     * - Nama: Jamaah
     * - Email: jamaah@example.com
     * - Password: Jamaah123!
     * - Role: jamaah
     * - Status: Aktif (Verified)
     */
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Ambil seluruh akun dengan role jamaah atau email jamaah@example.com
            $jamaahUsers = User::where('role', 'jamaah')
                ->orWhere('email', 'jamaah@example.com')
                ->get();

            // 2. Hapus seluruh akun jamaah beserta data & berkas relasinya
            foreach ($jamaahUsers as $user) {
                // Safety guard: Jangan pernah hapus akun admin
                if ($user->role === 'admin' || $user->email === 'admin@zeintour.com') {
                    continue;
                }

                // Hapus pendaftaran terkait (akan mentrigger event deleting untuk members & payments & file)
                foreach ($user->registrations as $registration) {
                    $registration->delete();
                }

                // Hapus user (akan mentrigger event deleting untuk avatar file)
                $user->delete();
            }

            // 3. Buat tepat satu akun Jamaah baru
            User::create([
                'name' => 'Jamaah',
                'email' => 'jamaah@example.com',
                'phone' => '6281234567890',
                'password' => Hash::make('Jamaah123!'),
                'role' => 'jamaah',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ]);
        });
    }
}
