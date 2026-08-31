<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\RegistrationCancellation;
use App\Models\RegistrationMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RandomRegistrationNumberTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $jamaah;
    protected Package $package;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@zeintour.com',
        ]);

        $this->jamaah = User::factory()->create([
            'role' => 'jamaah',
            'email' => 'jamaah.test@zeintour.com',
            'phone' => '6281234567890',
            'phone_verified_at' => now(),
        ]);

        $this->package = Package::create([
            'name' => 'Paket Umrah VIP Syawal 1447H',
            'slug' => 'paket-umrah-vip-syawal-1447h',
            'price' => 35000000,
            'departure_date' => now()->addDays(60),
            'duration' => 12,
            'quota' => 45,
            'facilities' => "Hotel Bintang 5\nTiket Saudia",
            'status' => 'aktif',
        ]);
    }

    /**
     * 1. Format REG-XXXXXX dengan 6 karakter acak (huruf kapital & angka) tanpa O, 0, I, 1, L.
     */
    public function test_registration_number_follows_exact_format_and_disallowed_char_rules(): void
    {
        $reg = Registration::create([
            'user_id' => $this->jamaah->id,
            'package_id' => $this->package->id,
            'status' => Registration::STATUS_MENUNGGU_VERIFIKASI_DOKUMEN,
        ]);

        $regNumber = $reg->registration_number;

        // Harus dimulai dengan prefix "REG-"
        $this->assertStringStartsWith('REG-', $regNumber);

        // Panjang total 10 karakter ("REG-" = 4 + 6 kode acak)
        $this->assertEquals(10, strlen($regNumber));

        $code = substr($regNumber, 4);
        $this->assertEquals(6, strlen($code));

        // Hanya karakter A-Z dan 2-9
        $this->assertMatchesRegularExpression('/^[A-Z0-9]{6}$/', $code);

        // Tidak boleh mengandung karakter ambigu: O, 0, I, 1, L
        $this->assertDoesNotMatch($code, 'O');
        $this->assertDoesNotMatch($code, '0');
        $this->assertDoesNotMatch($code, 'I');
        $this->assertDoesNotMatch($code, '1');
        $this->assertDoesNotMatch($code, 'L');
    }

    /**
     * 2. Minimal 25 pendaftaran acak berturut-turut semuanya unik dan tidak berurutan.
     */
    public function test_at_least_25_registrations_have_strictly_unique_random_numbers(): void
    {
        $generatedNumbers = [];
        $count = 25;

        for ($i = 0; $i < $count; $i++) {
            $user = User::factory()->create([
                'role' => 'jamaah',
                'email' => "user{$i}@test.com",
            ]);

            $reg = Registration::create([
                'user_id' => $user->id,
                'package_id' => $this->package->id,
                'status' => Registration::STATUS_MENUNGGU_VERIFIKASI_DOKUMEN,
            ]);

            $regNumber = $reg->registration_number;

            $this->assertFalse(
                in_array($regNumber, $generatedNumbers, true),
                "Duplicate registration number detected: {$regNumber}"
            );

            // Tidak boleh berurutan seperti REG-000001
            $this->assertDoesNotMatch($regNumber, '00000');

            $generatedNumbers[] = $regNumber;
        }

        $this->assertCount($count, array_unique($generatedNumbers));
    }

    /**
     * 3. Nomor pendaftaran permanen dan tidak berubah saat edit data, verifikasi, atau refresh.
     */
    public function test_registration_number_is_persistent_and_invariant(): void
    {
        $reg = Registration::create([
            'user_id' => $this->jamaah->id,
            'package_id' => $this->package->id,
            'status' => Registration::STATUS_MENUNGGU_VERIFIKASI_DOKUMEN,
        ]);

        $initialRegNumber = $reg->registration_number;
        $this->assertNotEmpty($initialRegNumber);

        // 1. Simpan anggota
        RegistrationMember::create([
            'registration_id' => $reg->id,
            'name' => 'Jamaah Utama',
            'nik' => '3273010101900001',
            'no_kk' => '3273010101909999',
            'relationship' => 'diri_sendiri',
            'document_status' => RegistrationMember::DOC_STATUS_MENUNGGU_VERIFIKASI,
        ]);

        $reg->refresh();
        $this->assertEquals($initialRegNumber, $reg->registration_number);

        // 2. Ubah status pendaftaran ke Menunggu DP
        $reg->update(['status' => Registration::STATUS_MENUNGGU_PEMBAYARAN_DP]);
        $reg->refresh();
        $this->assertEquals($initialRegNumber, $reg->registration_number);

        // 3. Tambah tagihan Invoice
        $invoice = Invoice::create([
            'registration_id' => $reg->id,
            'total_price' => 35000000,
            'total_paid' => 0,
            'remaining_balance' => 35000000,
            'due_date' => now()->addDays(7),
        ]);

        $reg->refresh();
        $this->assertEquals($initialRegNumber, $reg->registration_number);

        // 4. Pembayaran DP
        $payment = Payment::create([
            'registration_id' => $reg->id,
            'type' => Payment::TYPE_DP,
            'amount' => 10000000,
            'proof_file' => 'payments/proof.jpg',
            'status' => Payment::STATUS_DISETUJUI,
            'verified_at' => now(),
            'verified_by' => $this->admin->id,
        ]);

        $reg->refresh();
        $this->assertEquals($initialRegNumber, $reg->registration_number);

        // 5. Pembatalan pendaftaran
        RegistrationCancellation::create([
            'registration_id' => $reg->id,
            'user_id' => $this->jamaah->id,
            'reason' => 'Alasan mendesak',
            'status' => RegistrationCancellation::STATUS_APPROVED,
        ]);
        $reg->update([
            'status' => Registration::STATUS_DIBATALKAN,
            'cancellation_status' => 'approved',
        ]);

        $reg->refresh();
        $this->assertEquals($initialRegNumber, $reg->registration_number);
    }

    /**
     * 4. Database Unique Constraint aktif dan mencegah duplikasi.
     */
    public function test_database_enforces_unique_constraint_on_registration_number(): void
    {
        $reg1 = Registration::create([
            'user_id' => $this->jamaah->id,
            'package_id' => $this->package->id,
            'registration_number' => 'REG-9K7M4X',
            'status' => Registration::STATUS_MENUNGGU_VERIFIKASI_DOKUMEN,
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        $user2 = User::factory()->create(['role' => 'jamaah']);
        // Coba insert pendaftaran dengan registration_number yang sama persis
        DB::table('registrations')->insert([
            'user_id' => $user2->id,
            'package_id' => $this->package->id,
            'registration_number' => 'REG-9K7M4X',
            'status' => Registration::STATUS_MENUNGGU_VERIFIKASI_DOKUMEN,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Helper assertion untuk memastikan karakter tidak ditemukan.
     */
    protected function assertDoesNotMatch(string $haystack, string $needle): void
    {
        $this->assertFalse(
            str_contains($haystack, $needle),
            "String '{$haystack}' should NOT contain disallowed character '{$needle}'"
        );
    }
}
