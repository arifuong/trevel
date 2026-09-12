<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\PackageVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InactivePackagePublicFilterTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $jamaah;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->jamaah = User::factory()->create([
            'role' => 'jamaah',
        ]);
    }

    private function createMockFiles(): array
    {
        return [
            'ktp' => UploadedFile::fake()->image('ktp.jpg', 600, 400),
            'kk' => UploadedFile::fake()->image('kk.jpg', 600, 400),
        ];
    }

    public function test_1_and_2_active_packages_appear_on_public_pages_and_inactive_packages_are_hidden(): void
    {
        // Paket Aktif
        $activePackage = Package::create([
            'name' => 'PAKET UMROH AKTIF 2026',
            'slug' => 'paket-umroh-aktif-2026',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 9,
            'status' => 'aktif',
            'price' => 0,
            'facilities' => '-',
            'quota' => 20,
        ]);
        $activeVariant = $activePackage->variants()->create([
            'name' => 'VIP',
            'quota' => 20,
            'status' => 'aktif',
        ]);
        $activeVariant->prices()->create([
            'room_type' => 'quad',
            'normal_price' => 30000000,
            'is_active' => true,
        ]);

        // Paket Nonaktif
        $inactivePackage = Package::create([
            'name' => 'PAKET UMROH NONAKTIF RAHASIA',
            'slug' => 'paket-umroh-nonaktif-rahasia',
            'departure_date' => now()->addMonths(3)->format('Y-m-d'),
            'duration' => 12,
            'status' => 'nonaktif',
            'price' => 0,
            'facilities' => '-',
            'quota' => 20,
        ]);
        $inactiveVariant = $inactivePackage->variants()->create([
            'name' => 'VIP Nonaktif',
            'quota' => 20,
            'status' => 'nonaktif',
        ]);
        $inactiveVariant->prices()->create([
            'room_type' => 'quad',
            'normal_price' => 35000000,
            'is_active' => true,
        ]);

        // 1. Cek Landing Page (Home)
        $homeResponse = $this->get(route('home'));
        $homeResponse->assertOk();
        $homeResponse->assertSee('PAKET UMROH AKTIF 2026');
        $homeResponse->assertDontSee('PAKET UMROH NONAKTIF RAHASIA');

        // 2. Cek Halaman Katalog Lengkap (/paket)
        $katalogResponse = $this->get(route('paket'));
        $katalogResponse->assertOk();
        $katalogResponse->assertSee('PAKET UMROH AKTIF 2026');
        $katalogResponse->assertDontSee('PAKET UMROH NONAKTIF RAHASIA');
    }

    public function test_3_inactive_package_does_not_appear_in_registration_form_dropdown(): void
    {
        $activePackage = Package::create([
            'name' => 'PAKET FORM AKTIF',
            'slug' => 'paket-form-aktif',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 9,
            'status' => 'aktif',
            'price' => 0,
            'facilities' => '-',
            'quota' => 20,
        ]);
        $variant = $activePackage->variants()->create([
            'name' => 'VIP',
            'quota' => 20,
            'status' => 'aktif',
        ]);
        $variant->prices()->create([
            'room_type' => 'quad',
            'normal_price' => 30000000,
            'is_active' => true,
        ]);

        $inactivePackage = Package::create([
            'name' => 'PAKET FORM NONAKTIF',
            'slug' => 'paket-form-nonaktif',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 9,
            'status' => 'nonaktif',
            'price' => 0,
            'facilities' => '-',
            'quota' => 20,
        ]);

        $response = $this->actingAs($this->jamaah)->get(route('jamaah.registration.create'));
        $response->assertOk();
        $response->assertSee('PAKET FORM AKTIF');
        $response->assertDontSee('PAKET FORM NONAKTIF');
    }

    public function test_4_direct_url_to_inactive_package_returns_404(): void
    {
        $inactivePackage = Package::create([
            'name' => 'PAKET HIDDEN DETAIL',
            'slug' => 'paket-hidden-detail',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 9,
            'status' => 'nonaktif',
            'price' => 0,
            'facilities' => '-',
            'quota' => 20,
        ]);

        $response = $this->get(route('paket.detail', $inactivePackage->slug));
        $response->assertNotFound();
    }

    public function test_5_passing_inactive_package_in_query_params_does_not_select_it(): void
    {
        $activePackage = Package::create([
            'name' => 'PAKET DEFAULT AKTIF',
            'slug' => 'paket-default-aktif',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 9,
            'status' => 'aktif',
            'price' => 0,
            'facilities' => '-',
            'quota' => 20,
        ]);
        $activePackage->variants()->create([
            'name' => 'VIP',
            'quota' => 20,
            'status' => 'aktif',
        ]);

        $inactivePackage = Package::create([
            'name' => 'PAKET NONAKTIF QUERY',
            'slug' => 'paket-nonaktif-query',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 9,
            'status' => 'nonaktif',
            'price' => 0,
            'facilities' => '-',
            'quota' => 20,
        ]);

        // Coba buka form pendaftaran dengan parameter paket nonaktif
        $response = $this->actingAs($this->jamaah)->get(route('jamaah.registration.create', ['package_id' => $inactivePackage->id]));
        $response->assertOk();
        $response->assertDontSee('PAKET NONAKTIF QUERY');
        $response->assertSee('PAKET DEFAULT AKTIF');
    }

    public function test_6_manual_post_request_with_inactive_package_id_is_strictly_rejected(): void
    {
        $inactivePackage = Package::create([
            'name' => 'PAKET REJECT POST',
            'slug' => 'paket-reject-post',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 9,
            'status' => 'nonaktif',
            'price' => 0,
            'facilities' => '-',
            'quota' => 20,
        ]);
        $variant = $inactivePackage->variants()->create([
            'name' => 'VIP',
            'quota' => 20,
            'status' => 'aktif',
        ]);
        $variant->prices()->create([
            'room_type' => 'quad',
            'normal_price' => 30000000,
            'is_active' => true,
        ]);

        $files = $this->createMockFiles();

        $response = $this->actingAs($this->jamaah)->post(route('jamaah.registration.store'), [
            'package_id' => $inactivePackage->id,
            'package_variant_id' => $variant->id,
            'room_type' => 'quad',
            'members' => [
                [
                    'name' => 'Jamaah Test',
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

        $response->assertSessionHasErrors(['package_id']);
        $this->assertDatabaseMissing('registrations', [
            'user_id' => $this->jamaah->id,
            'package_id' => $inactivePackage->id,
        ]);
    }

    public function test_7_manual_post_request_with_inactive_variant_id_is_strictly_rejected(): void
    {
        $activePackage = Package::create([
            'name' => 'PAKET AKTIF DENGAN VARIAN NONAKTIF',
            'slug' => 'paket-aktif-varian-nonaktif',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 9,
            'status' => 'aktif',
            'price' => 0,
            'facilities' => '-',
            'quota' => 20,
        ]);
        $inactiveVariant = $activePackage->variants()->create([
            'name' => 'VIP Nonaktif',
            'quota' => 20,
            'status' => 'nonaktif',
        ]);
        $inactiveVariant->prices()->create([
            'room_type' => 'quad',
            'normal_price' => 30000000,
            'is_active' => true,
        ]);

        $files = $this->createMockFiles();

        $response = $this->actingAs($this->jamaah)->post(route('jamaah.registration.store'), [
            'package_id' => $activePackage->id,
            'package_variant_id' => $inactiveVariant->id,
            'room_type' => 'quad',
            'members' => [
                [
                    'name' => 'Jamaah Test',
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

        $response->assertSessionHasErrors(['package_variant_id']);
        $this->assertDatabaseMissing('registrations', [
            'user_id' => $this->jamaah->id,
        ]);
    }

    public function test_8_admin_can_still_view_and_manage_inactive_packages(): void
    {
        $inactivePackage = Package::create([
            'name' => 'PAKET ADMIN LIHAT NONAKTIF',
            'slug' => 'paket-admin-lihat-nonaktif',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 9,
            'status' => 'nonaktif',
            'price' => 0,
            'facilities' => '-',
            'quota' => 20,
        ]);

        // 1. Admin Index Daftar Paket
        $indexResponse = $this->actingAs($this->admin)->get(route('admin.packages.index'));
        $indexResponse->assertOk();
        $indexResponse->assertSee('PAKET ADMIN LIHAT NONAKTIF');

        // 2. Admin Show Detail Paket
        $showResponse = $this->actingAs($this->admin)->get(route('admin.packages.show', $inactivePackage));
        $showResponse->assertOk();
        $showResponse->assertSee('PAKET ADMIN LIHAT NONAKTIF');
    }

    public function test_9_and_10_status_toggle_between_active_and_inactive_immediately_affects_public(): void
    {
        $package = Package::create([
            'name' => 'PAKET TOGGLE STATUS',
            'slug' => 'paket-toggle-status',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 9,
            'status' => 'aktif',
            'price' => 0,
            'facilities' => '-',
            'quota' => 20,
        ]);
        $variant = $package->variants()->create([
            'name' => 'VIP',
            'quota' => 20,
            'status' => 'aktif',
        ]);
        $variant->prices()->create([
            'room_type' => 'quad',
            'normal_price' => 30000000,
            'is_active' => true,
        ]);

        // State 1: Aktif -> Muncul di publik
        $this->get(route('home'))->assertSee('PAKET TOGGLE STATUS');
        $this->get(route('paket.detail', $package->slug))->assertOk();

        // Admin ubah status menjadi Nonaktif
        $package->update(['status' => 'nonaktif']);

        // State 2: Nonaktif -> Langsung hilang dari publik dan detail 404
        $this->get(route('home'))->assertDontSee('PAKET TOGGLE STATUS');
        $this->get(route('paket'))->assertDontSee('PAKET TOGGLE STATUS');
        $this->get(route('paket.detail', $package->slug))->assertNotFound();

        // Admin ubah status kembali menjadi Aktif
        $package->update(['status' => 'aktif']);

        // State 3: Aktif kembali -> Langsung muncul kembali di publik
        $this->get(route('home'))->assertSee('PAKET TOGGLE STATUS');
        $this->get(route('paket'))->assertSee('PAKET TOGGLE STATUS');
        $this->get(route('paket.detail', $package->slug))->assertOk();
    }
}
