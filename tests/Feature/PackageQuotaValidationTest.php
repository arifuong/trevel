<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\PackageVariant;
use App\Models\Registration;
use App\Models\RegistrationCancellation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PackageQuotaValidationTest extends TestCase
{
    use RefreshDatabase;

    private User $jamaah1;
    private User $jamaah2;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->jamaah1 = User::factory()->create([
            'role' => 'jamaah',
            'phone_verified_at' => now(),
        ]);

        $this->jamaah2 = User::factory()->create([
            'role' => 'jamaah',
            'phone_verified_at' => now(),
        ]);

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'phone_verified_at' => now(),
        ]);
    }

    private function createMockFiles(): array
    {
        return [
            'ktp' => UploadedFile::fake()->image('ktp.jpg', 600, 400),
            'kk' => UploadedFile::fake()->image('kk.jpg', 600, 400),
        ];
    }

    public function test_1_registration_succeeds_when_package_and_variant_have_available_seats(): void
    {
        // Paket induk dibuat dengan quota = 0 (karena kuota diatur per sub-paket)
        $package = Package::create([
            'name' => 'PAKET UMROH REGULER 12 HARI',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 12,
            'status' => 'aktif',
            'price' => 0,
            'facilities' => '-',
            'quota' => 0,
        ]);

        // Varian VIP kuota 20
        $variant = $package->variants()->create([
            'name' => 'VIP',
            'quota' => 20,
            'status' => 'aktif',
        ]);

        $variant->prices()->create([
            'room_type' => 'quad',
            'normal_price' => 35000000,
            'is_active' => true,
        ]);

        $files = $this->createMockFiles();

        $response = $this->actingAs($this->jamaah1)->post(route('jamaah.registration.store'), [
            'package_id' => $package->id,
            'package_variant_id' => $variant->id,
            'room_type' => 'quad',
            'members' => [
                [
                    'name' => 'Jamaah Satu',
                    'birth_place' => 'Bandung',
                    'birth_date' => '1990-01-01',
                    'gender' => 'laki-laki',
                    'address' => 'Jl. Merdeka No. 1',
                    'nik' => '3273010101900001',
                    'no_kk' => '3273010101900000',
                    'relationship' => 'diri_sendiri',
                    'ktp_file' => $files['ktp'],
                    'kk_file' => $files['kk'],
                ],
            ],
        ]);

        $response->assertRedirect(route('jamaah.my-registration'));
        $this->assertDatabaseHas('registrations', [
            'user_id' => $this->jamaah1->id,
            'package_id' => $package->id,
            'package_variant_id' => $variant->id,
        ]);

        // Sisa kuota varian = 20 - 1 = 19
        $this->assertEquals(19, $variant->getRemainingQuota());
        $this->assertEquals(19, $package->getRemainingQuota());
    }

    public function test_2_registration_succeeds_when_exactly_1_seat_remains(): void
    {
        $package = Package::create([
            'name' => 'PAKET UMROH SPESIAL',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 9,
            'status' => 'aktif',
            'price' => 0,
            'facilities' => '-',
            'quota' => 0,
        ]);

        $variant = $package->variants()->create([
            'name' => 'Bisnis',
            'quota' => 1, // Tepat 1 kursi
            'status' => 'aktif',
        ]);

        $variant->prices()->create([
            'room_type' => 'quad',
            'normal_price' => 30000000,
            'is_active' => true,
        ]);

        $files = $this->createMockFiles();

        $response = $this->actingAs($this->jamaah1)->post(route('jamaah.registration.store'), [
            'package_id' => $package->id,
            'package_variant_id' => $variant->id,
            'room_type' => 'quad',
            'members' => [
                [
                    'name' => 'Jamaah Tunggal',
                    'birth_place' => 'Bandung',
                    'birth_date' => '1990-01-01',
                    'gender' => 'laki-laki',
                    'address' => 'Jl. Merdeka No. 1',
                    'nik' => '3273010101900001',
                    'no_kk' => '3273010101900000',
                    'relationship' => 'diri_sendiri',
                    'ktp_file' => $files['ktp'],
                    'kk_file' => $files['kk'],
                ],
            ],
        ]);

        $response->assertRedirect(route('jamaah.my-registration'));
        $this->assertEquals(0, $variant->getRemainingQuota());
        $this->assertTrue($variant->is_sold_out);
    }

    public function test_3_registration_rejected_when_quota_is_0(): void
    {
        $package = Package::create([
            'name' => 'PAKET UMROH HABIS',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 9,
            'status' => 'aktif',
            'price' => 0,
            'facilities' => '-',
            'quota' => 0,
        ]);

        $variant = $package->variants()->create([
            'name' => 'Ekonomi',
            'quota' => 0, // 0 kursi
            'status' => 'aktif',
        ]);

        $variant->prices()->create([
            'room_type' => 'quad',
            'normal_price' => 25000000,
            'is_active' => true,
        ]);

        $files = $this->createMockFiles();

        $response = $this->actingAs($this->jamaah1)->post(route('jamaah.registration.store'), [
            'package_id' => $package->id,
            'package_variant_id' => $variant->id,
            'room_type' => 'quad',
            'members' => [
                [
                    'name' => 'Jamaah Test',
                    'nik' => '3273010101900001',
                    'no_kk' => '3273010101900000',
                    'relationship' => 'diri_sendiri',
                    'ktp_file' => $files['ktp'],
                    'kk_file' => $files['kk'],
                ],
            ],
        ]);

        $response->assertSessionHasErrors('package_variant_id');
        $this->assertDatabaseMissing('registrations', [
            'user_id' => $this->jamaah1->id,
        ]);
    }

    public function test_4_registration_rejected_when_registering_2_members_but_only_1_seat_remains(): void
    {
        $package = Package::create([
            'name' => 'PAKET UMROH KUOTA 1',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 9,
            'status' => 'aktif',
            'price' => 0,
            'facilities' => '-',
            'quota' => 0,
        ]);

        $variant = $package->variants()->create([
            'name' => 'VIP',
            'quota' => 1, // Sisa 1 kursi
            'status' => 'aktif',
        ]);

        $variant->prices()->create([
            'room_type' => 'quad',
            'normal_price' => 35000000,
            'is_active' => true,
        ]);

        $files = $this->createMockFiles();

        // Mendaftar 2 orang padahal sisa 1
        $response = $this->actingAs($this->jamaah1)->post(route('jamaah.registration.store'), [
            'package_id' => $package->id,
            'package_variant_id' => $variant->id,
            'room_type' => 'quad',
            'members' => [
                [
                    'name' => 'Jamaah Satu',
                    'nik' => '3273010101900001',
                    'no_kk' => '3273010101900000',
                    'relationship' => 'diri_sendiri',
                    'ktp_file' => $files['ktp'],
                    'kk_file' => $files['kk'],
                ],
                [
                    'name' => 'Jamaah Dua',
                    'nik' => '3273010202920002',
                    'no_kk' => '3273010101900000',
                    'relationship' => 'istri',
                    'ktp_file' => $files['ktp'],
                    'kk_file' => $files['kk'],
                ],
            ],
        ]);

        $response->assertSessionHasErrors(['package_variant_id']);
        $errorMsg = session('errors')->get('package_variant_id')[0];
        $this->assertStringContainsString('Sisa kuota sub-paket (1 kursi) tidak mencukupi untuk mendaftarkan 2 jamaah', $errorMsg);
    }

    public function test_5_cancelled_registration_does_not_reduce_quota(): void
    {
        $package = Package::create([
            'name' => 'PAKET UMROH CANCEL TEST',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 12,
            'status' => 'aktif',
            'price' => 0,
            'facilities' => '-',
            'quota' => 0,
        ]);

        $variant = $package->variants()->create([
            'name' => 'VIP',
            'quota' => 5,
            'status' => 'aktif',
        ]);

        $variant->prices()->create([
            'room_type' => 'quad',
            'normal_price' => 35000000,
            'is_active' => true,
        ]);

        // Buat pendaftaran yang sudah dibatalkan
        $regCancelled = Registration::create([
            'user_id' => $this->jamaah2->id,
            'package_id' => $package->id,
            'package_variant_id' => $variant->id,
            'room_type' => 'quad',
            'status' => Registration::STATUS_DIBATALKAN,
            'cancellation_status' => RegistrationCancellation::STATUS_APPROVED,
        ]);

        $regCancelled->members()->create([
            'name' => 'Jamaah Cancelled',
            'nik' => '3273010101900002',
            'no_kk' => '3273010101900000',
            'relationship' => 'diri_sendiri',
        ]);

        // Sisa kuota tetap 5 (karena pendaftaran berstatus dibatalkan)
        $this->assertEquals(5, $variant->getRemainingQuota());
        $this->assertEquals(5, $package->getRemainingQuota());
    }

    public function test_6_ignore_self_when_recalculating_during_edit(): void
    {
        $package = Package::create([
            'name' => 'PAKET EDIT TEST',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 12,
            'status' => 'aktif',
            'price' => 0,
            'facilities' => '-',
            'quota' => 0,
        ]);

        $variant = $package->variants()->create([
            'name' => 'VIP',
            'quota' => 10,
            'status' => 'aktif',
        ]);

        // Pendaftaran 3 jamaah aktif
        $reg = Registration::create([
            'user_id' => $this->jamaah1->id,
            'package_id' => $package->id,
            'package_variant_id' => $variant->id,
            'room_type' => 'quad',
            'status' => Registration::STATUS_JAMAAH,
        ]);

        for ($i = 1; $i <= 3; $i++) {
            $reg->members()->create([
                'name' => "Jamaah $i",
                'nik' => "327301010190000$i",
                'no_kk' => '3273010101900000',
                'relationship' => $i === 1 ? 'diri_sendiri' : 'anak',
            ]);
        }

        // Secara umum sisa kuota = 10 - 3 = 7
        $this->assertEquals(7, $variant->getRemainingQuota());

        // Jika pendaftaran $reg sedang diedit, sisa kuota untuk konteks $reg adalah 10 (mengabaikan pendaftaran itu sendiri)
        $this->assertEquals(10, $variant->getRemainingQuota($reg->id));
    }

    public function test_7_multiple_variants_track_quotas_independently(): void
    {
        $package = Package::create([
            'name' => 'PAKET MULTI VARIAN',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 12,
            'status' => 'aktif',
            'price' => 0,
            'facilities' => '-',
            'quota' => 0,
        ]);

        $vip = $package->variants()->create(['name' => 'VIP', 'quota' => 10, 'status' => 'aktif']);
        $bisnis = $package->variants()->create(['name' => 'Bisnis', 'quota' => 15, 'status' => 'aktif']);
        $ekonomi = $package->variants()->create(['name' => 'Ekonomi', 'quota' => 20, 'status' => 'aktif']);

        // Register 4 jamaah to VIP
        $regVip = Registration::create([
            'user_id' => $this->jamaah1->id,
            'package_id' => $package->id,
            'package_variant_id' => $vip->id,
            'room_type' => 'quad',
            'status' => Registration::STATUS_JAMAAH,
        ]);
        for ($i = 1; $i <= 4; $i++) {
            $regVip->members()->create(['name' => "VIP $i", 'nik' => "327301010190001$i", 'no_kk' => '3273010101900000', 'relationship' => 'diri_sendiri']);
        }

        // Register 5 jamaah to Bisnis
        $regBisnis = Registration::create([
            'user_id' => $this->jamaah2->id,
            'package_id' => $package->id,
            'package_variant_id' => $bisnis->id,
            'room_type' => 'quad',
            'status' => Registration::STATUS_MENUNGGU_PEMBAYARAN_DP,
        ]);
        for ($i = 1; $i <= 5; $i++) {
            $regBisnis->members()->create(['name' => "Bisnis $i", 'nik' => "327301010190002$i", 'no_kk' => '3273010101900000', 'relationship' => 'diri_sendiri']);
        }

        // Check independent quotas
        $this->assertEquals(6, $vip->getRemainingQuota());     // 10 - 4 = 6
        $this->assertEquals(10, $bisnis->getRemainingQuota()); // 15 - 5 = 10
        $this->assertEquals(20, $ekonomi->getRemainingQuota());// 20 - 0 = 20

        // Parent package total & remaining
        $this->assertEquals(45, $package->getTotalQuota());    // 10 + 15 + 20 = 45
        $this->assertEquals(36, $package->getRemainingQuota());// 6 + 10 + 20 = 36
    }

    public function test_8_create_form_view_receives_active_packages_with_remaining_seats(): void
    {
        $package = Package::create([
            'name' => 'PAKET VIEW TEST',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 12,
            'status' => 'aktif',
            'price' => 0,
            'facilities' => '-',
            'quota' => 0,
        ]);

        $variant = $package->variants()->create(['name' => 'VIP', 'quota' => 15, 'status' => 'aktif']);
        $variant->prices()->create(['room_type' => 'quad', 'normal_price' => 35000000, 'is_active' => true]);

        $response = $this->actingAs($this->jamaah1)->get(route('jamaah.registration.create'));
        $response->assertOk();
        $response->assertSee('PAKET VIEW TEST');
        $response->assertSee('VIP');
    }

    public function test_9_landing_page_displays_live_seats_available(): void
    {
        $package = Package::create([
            'name' => 'PAKET LANDING TEST',
            'slug' => 'paket-landing-test',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 12,
            'status' => 'aktif',
            'price' => 0,
            'facilities' => '-',
            'quota' => 0,
        ]);

        $variant = $package->variants()->create(['name' => 'VIP', 'quota' => 20, 'status' => 'aktif']);
        $variant->prices()->create(['room_type' => 'quad', 'normal_price' => 35000000, 'is_active' => true]);

        // Register 5 jamaah
        $reg = Registration::create([
            'user_id' => $this->jamaah1->id,
            'package_id' => $package->id,
            'package_variant_id' => $variant->id,
            'room_type' => 'quad',
            'status' => Registration::STATUS_JAMAAH,
        ]);
        for ($i = 1; $i <= 5; $i++) {
            $reg->members()->create(['name' => "Jamaah $i", 'nik' => "327301010190003$i", 'no_kk' => '3273010101900000', 'relationship' => 'diri_sendiri']);
        }

        // Verify direct model remaining quota
        $this->assertEquals(15, $variant->getRemainingQuota());
        $this->assertEquals(15, $package->getRemainingQuota());

        $response = $this->get(route('paket.detail', $package->slug));
        $response->assertOk();
        $response->assertSee('PAKET LANDING TEST');
    }
}
