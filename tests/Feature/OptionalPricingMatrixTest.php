<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\PackageVariant;
use App\Models\PackageVariantPrice;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OptionalPricingMatrixTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $jamaah;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->jamaah = User::factory()->create([
            'role' => 'jamaah',
        ]);
    }

    public function test_admin_can_save_variant_with_only_one_or_two_active_room_types(): void
    {
        $package = Package::create([
            'name' => 'Paket Umroh Reguler 9 Hari',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 9,
            'description' => 'Paket umroh 9 hari',
            'status' => 'aktif',
            'price' => 0,
            'facilities' => '-',
            'quota' => 40,
        ]);

        // Sub-paket Ekonomi: Quad & Triple aktif, Double nonaktif tanpa harga
        $response = $this->actingAs($this->admin)->post(route('admin.packages.variants.store', $package), [
            'name' => 'Ekonomi',
            'quota' => 20,
            'status' => 'aktif',
            'sort_order' => 1,
            'prices' => [
                ['room_type' => 'quad', 'normal_price' => 30000000, 'promo_price' => null, 'is_active' => '1'],
                ['room_type' => 'triple', 'normal_price' => 32000000, 'promo_price' => null, 'is_active' => '1'],
                ['room_type' => 'double', 'normal_price' => '', 'promo_price' => '', 'is_active' => '0'],
            ],
        ]);

        $response->assertRedirect(route('admin.packages.show', $package));

        $variant = PackageVariant::where('package_id', $package->id)->where('name', 'Ekonomi')->first();
        $this->assertNotNull($variant);

        $quad = $variant->prices()->where('room_type', 'quad')->first();
        $triple = $variant->prices()->where('room_type', 'triple')->first();
        $double = $variant->prices()->where('room_type', 'double')->first();

        $this->assertTrue((bool) $quad->is_active);
        $this->assertEquals(30000000, (float) $quad->normal_price);

        $this->assertTrue((bool) $triple->is_active);
        $this->assertEquals(32000000, (float) $triple->normal_price);

        $this->assertFalse((bool) $double->is_active);
    }

    public function test_validation_fails_if_active_room_type_has_no_normal_price_or_zero(): void
    {
        $package = Package::create([
            'name' => 'Paket Umroh Reguler 9 Hari',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 9,
            'status' => 'aktif',
            'price' => 0,
            'facilities' => '-',
            'quota' => 40,
        ]);

        // Aktif tapi normal_price kosong
        $response = $this->actingAs($this->admin)->post(route('admin.packages.variants.store', $package), [
            'name' => 'VIP',
            'quota' => 20,
            'status' => 'aktif',
            'prices' => [
                ['room_type' => 'quad', 'normal_price' => '', 'is_active' => '1'],
                ['room_type' => 'triple', 'normal_price' => 40000000, 'is_active' => '1'],
                ['room_type' => 'double', 'normal_price' => '', 'is_active' => '0'],
            ],
        ]);

        $response->assertSessionHasErrors(['prices.0.normal_price']);
    }

    public function test_deactivating_room_preserves_price_data_in_db(): void
    {
        $package = Package::create([
            'name' => 'Paket Umroh 12 Hari',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 12,
            'status' => 'aktif',
            'price' => 0,
            'facilities' => '-',
            'quota' => 40,
        ]);

        $variant = $package->variants()->create([
            'name' => 'VIP',
            'quota' => 20,
            'status' => 'aktif',
        ]);

        $doublePrice = $variant->prices()->create([
            'room_type' => 'double',
            'normal_price' => 45000000,
            'promo_price' => 43000000,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        // Admin menonaktifkan Double dan mengosongkan input harga di form
        $response = $this->actingAs($this->admin)->put(route('admin.packages.variants.update', [$package, $variant]), [
            'name' => 'VIP',
            'quota' => 20,
            'status' => 'aktif',
            'prices' => [
                [
                    'id' => $doublePrice->id,
                    'room_type' => 'double',
                    'normal_price' => '',
                    'promo_price' => '',
                    'is_active' => '0',
                ],
            ],
        ]);

        $response->assertRedirect(route('admin.packages.show', $package));

        $doublePrice->refresh();
        $this->assertFalse((bool) $doublePrice->is_active);
        // Harga tersimpan sebelumnya tetap dipertahankan
        $this->assertEquals(45000000, (float) $doublePrice->normal_price);
    }

    public function test_lowest_price_accessor_ignores_inactive_room_types(): void
    {
        $package = Package::create([
            'name' => 'Paket Umroh 12 Hari',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 12,
            'status' => 'aktif',
            'price' => 0,
            'facilities' => '-',
            'quota' => 40,
        ]);

        $variant = $package->variants()->create([
            'name' => 'VIP',
            'quota' => 20,
            'status' => 'aktif',
        ]);

        // Quad dinonaktifkan (harga 30jt), Triple aktif (40jt), Double aktif (45jt)
        $variant->prices()->createMany([
            ['room_type' => 'quad', 'normal_price' => 30000000, 'is_active' => false],
            ['room_type' => 'triple', 'normal_price' => 40000000, 'is_active' => true],
            ['room_type' => 'double', 'normal_price' => 45000000, 'is_active' => true],
        ]);

        $variant->refresh();
        // Lowest price variant harus 40jt (Quad diabaikan karena is_active = false)
        $this->assertEquals(40000000, $variant->lowest_price);

        $package->refresh();
        // Lowest price package juga harus 40jt
        $this->assertEquals(40000000, $package->lowest_price);
    }

    public function test_jamaah_registration_fails_if_attempting_to_register_inactive_room_type(): void
    {
        Storage::fake('public');

        $package = Package::create([
            'name' => 'Paket Umroh 12 Hari',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 12,
            'status' => 'aktif',
            'price' => 0,
            'facilities' => '-',
            'quota' => 40,
        ]);

        $variant = $package->variants()->create([
            'name' => 'Ekonomi',
            'quota' => 20,
            'status' => 'aktif',
        ]);

        // Quad aktif, Double dinonaktifkan
        $variant->prices()->createMany([
            ['room_type' => 'quad', 'normal_price' => 30000000, 'is_active' => true],
            ['room_type' => 'double', 'normal_price' => 35000000, 'is_active' => false],
        ]);

        $ktpFile = UploadedFile::fake()->image('ktp.jpg', 600, 400);
        $kkFile = UploadedFile::fake()->image('kk.jpg', 600, 400);

        // Jamaah mencoba mendaftar dengan room_type = double (yang dinonaktifkan)
        $response = $this->actingAs($this->jamaah)->post(route('jamaah.registration.store'), [
            'package_id' => $package->id,
            'package_variant_id' => $variant->id,
            'room_type' => 'double',
            'members' => [
                [
                    'name' => 'Jamaah Test',
                    'birth_place' => 'Bandung',
                    'birth_date' => '1990-01-01',
                    'gender' => 'laki-laki',
                    'nik' => '3273010101900001',
                    'address' => 'Jl. Merdeka No 1',
                    'no_kk' => '3273010101900000',
                    'relationship' => 'diri_sendiri',
                    'ktp_file' => $ktpFile,
                    'kk_file' => $kkFile,
                ],
            ],
        ]);

        $response->assertSessionHasErrors(['room_type']);
        $this->assertDatabaseMissing('registrations', [
            'user_id' => $this->jamaah->id,
            'room_type' => 'double',
        ]);
    }

    public function test_jamaah_registration_succeeds_with_active_room_type(): void
    {
        Storage::fake('public');

        $package = Package::create([
            'name' => 'Paket Umroh 12 Hari',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 12,
            'status' => 'aktif',
            'price' => 0,
            'facilities' => '-',
            'quota' => 40,
        ]);

        $variant = $package->variants()->create([
            'name' => 'Ekonomi',
            'quota' => 20,
            'status' => 'aktif',
        ]);

        // Quad aktif 30jt, Double nonaktif
        $variant->prices()->createMany([
            ['room_type' => 'quad', 'normal_price' => 30000000, 'is_active' => true],
            ['room_type' => 'double', 'normal_price' => 35000000, 'is_active' => false],
        ]);

        $ktpFile = UploadedFile::fake()->image('ktp.jpg', 600, 400);
        $kkFile = UploadedFile::fake()->image('kk.jpg', 600, 400);

        $response = $this->actingAs($this->jamaah)->post(route('jamaah.registration.store'), [
            'package_id' => $package->id,
            'package_variant_id' => $variant->id,
            'room_type' => 'quad',
            'members' => [
                [
                    'name' => 'Jamaah Test',
                    'birth_place' => 'Bandung',
                    'birth_date' => '1990-01-01',
                    'gender' => 'laki-laki',
                    'nik' => '3273010101900001',
                    'address' => 'Jl. Merdeka No 1',
                    'no_kk' => '3273010101900000',
                    'relationship' => 'diri_sendiri',
                    'ktp_file' => $ktpFile,
                    'kk_file' => $kkFile,
                ],
            ],
        ]);

        $response->assertRedirect(route('jamaah.my-registration'));
        $this->assertDatabaseHas('registrations', [
            'user_id' => $this->jamaah->id,
            'room_type' => 'quad',
        ]);
    }
}
