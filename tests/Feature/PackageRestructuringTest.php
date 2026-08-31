<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\PackageVariant;
use App\Models\PackageVariantPrice;
use App\Models\Registration;
use App\Models\RegistrationMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PackageRestructuringTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $jamaah;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'phone_verified_at' => now(),
        ]);

        $this->jamaah = User::factory()->create([
            'role' => 'jamaah',
            'phone_verified_at' => now(),
        ]);
    }

    public function test_admin_can_create_parent_package(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.packages.store'), [
            'name' => 'Paket Umroh Spesial 12 Hari',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 12,
            'description' => 'Paket umroh 12 hari terbaik dengan bimbingan ibadah berpengalaman.',
            'status' => 'aktif',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('packages', [
            'name' => 'Paket Umroh Spesial 12 Hari',
            'duration' => 12,
            'status' => 'aktif',
        ]);

        $package = Package::where('name', 'Paket Umroh Spesial 12 Hari')->first();
        $this->assertNotNull($package->slug);
    }

    public function test_admin_can_add_sub_packages_vip_bisnis_ekonomi_with_matrix_pricing(): void
    {
        $package = Package::create([
            'name' => 'Paket Umroh Spesial 12 Hari',
            'slug' => 'paket-umroh-spesial-12-hari',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 12,
            'description' => 'Deskripsi paket induk.',
            'status' => 'aktif',
            'price' => 0,
            'facilities' => '-',
            'quota' => 0,
        ]);

        // 1. Tambah Sub-Paket VIP
        $vipResponse = $this->actingAs($this->admin)->post(route('admin.packages.variants.store', $package), [
            'name' => 'VIP',
            'description' => 'Layanan VIP Bintang 5 Ring 1',
            'quota' => 15,
            'status' => 'aktif',
            'sort_order' => 1,
            'airline_departure' => 'Saudia Airlines Direct',
            'airline_return' => 'Saudia Airlines Direct',
            'hotel_makkah_name' => 'Dar Al Eiman Royal',
            'hotel_makkah_star' => 'Bintang 5',
            'hotel_madinah_name' => 'Millennium Al Aqiq',
            'hotel_madinah_star' => 'Bintang 5',
            'prices' => [
                ['room_type' => 'quad', 'normal_price' => 42000000, 'promo_price' => 41000000, 'is_active' => '1'],
                ['room_type' => 'triple', 'normal_price' => 45000000, 'promo_price' => null, 'is_active' => '1'],
                ['room_type' => 'double', 'normal_price' => 49000000, 'promo_price' => null, 'is_active' => '1'],
            ],
            'includes' => ['Tiket Pesawat PP', 'Visa Umrah', 'Lounge Bandara'],
            'excludes' => ['Paspor', 'Vaksin'],
        ]);

        $vipResponse->assertRedirect(route('admin.packages.show', $package));

        $this->assertDatabaseHas('package_variants', [
            'package_id' => $package->id,
            'name' => 'VIP',
            'quota' => 15,
            'hotel_makkah_name' => 'Dar Al Eiman Royal',
        ]);

        $vipVariant = PackageVariant::where('package_id', $package->id)->where('name', 'VIP')->first();
        $this->assertCount(3, $vipVariant->prices);
        $this->assertEquals(41000000, $vipVariant->lowest_price);
        $this->assertCount(3, $vipVariant->includes);
        $this->assertCount(2, $vipVariant->excludes);

        // 2. Tambah Sub-Paket Bisnis
        $this->actingAs($this->admin)->post(route('admin.packages.variants.store', $package), [
            'name' => 'Bisnis',
            'quota' => 20,
            'status' => 'aktif',
            'sort_order' => 2,
            'prices' => [
                ['room_type' => 'quad', 'normal_price' => 36000000, 'promo_price' => 35000000, 'is_active' => '1'],
                ['room_type' => 'triple', 'normal_price' => 38000000, 'promo_price' => null, 'is_active' => '1'],
                ['room_type' => 'double', 'normal_price' => 41000000, 'promo_price' => null, 'is_active' => '1'],
            ],
        ]);

        // 3. Tambah Sub-Paket Ekonomi
        $this->actingAs($this->admin)->post(route('admin.packages.variants.store', $package), [
            'name' => 'Ekonomi',
            'quota' => 25,
            'status' => 'aktif',
            'sort_order' => 3,
            'prices' => [
                ['room_type' => 'quad', 'normal_price' => 29000000, 'promo_price' => 28500000, 'is_active' => '1'],
                ['room_type' => 'triple', 'normal_price' => 31000000, 'promo_price' => null, 'is_active' => '1'],
                ['room_type' => 'double', 'normal_price' => 34000000, 'promo_price' => null, 'is_active' => '1'],
            ],
        ]);

        // Cek bahwa Paket Induk memiliki 3 sub-paket
        $package->refresh();
        $this->assertCount(3, $package->variants);
        // Lowest price paket induk harus diambil dari varian dengan harga terendah (Ekonomi Quad Promo = 28.500.000)
        $this->assertEquals(28500000, $package->lowest_price);
    }

    public function test_public_detail_page_displays_parent_and_all_variants(): void
    {
        $package = Package::create([
            'name' => 'Paket Umroh Spesial 12 Hari',
            'slug' => 'paket-umroh-spesial-12-hari',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 12,
            'description' => 'Deskripsi paket induk.',
            'status' => 'aktif',
            'price' => 0,
            'facilities' => '-',
            'quota' => 0,
        ]);

        $variant = $package->variants()->create([
            'name' => 'VIP',
            'slug' => 'vip-12h',
            'quota' => 10,
            'status' => 'aktif',
            'sort_order' => 1,
            'hotel_makkah_name' => 'Pullman Zamzam',
        ]);

        $variant->prices()->create([
            'room_type' => 'quad',
            'normal_price' => 41000000,
            'is_active' => true,
        ]);

        $response = $this->get(route('paket.detail', $package->slug));
        $response->assertStatus(200);
        $response->assertSee('Paket Umroh Spesial 12 Hari');
        $response->assertSee('VIP');
    }

    public function test_jamaah_registration_with_variant_and_room_type_calculates_correct_invoice(): void
    {
        Storage::fake('public');

        $package = Package::create([
            'name' => 'Paket Umroh Spesial 12 Hari',
            'slug' => 'paket-umroh-spesial-12-hari',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 12,
            'status' => 'aktif',
            'price' => 0,
            'facilities' => '-',
            'quota' => 50,
        ]);

        $variant = $package->variants()->create([
            'name' => 'VIP',
            'slug' => 'vip-12h',
            'quota' => 10,
            'status' => 'aktif',
            'sort_order' => 1,
        ]);

        // Harga Quad = 40.000.000, Triple = 45.000.000
        $variant->prices()->createMany([
            ['room_type' => 'quad', 'normal_price' => 40000000, 'is_active' => true],
            ['room_type' => 'triple', 'normal_price' => 45000000, 'promo_price' => 43000000, 'is_active' => true],
            ['room_type' => 'double', 'normal_price' => 50000000, 'is_active' => true],
        ]);

        $ktpFile = UploadedFile::fake()->image('ktp.jpg', 600, 400);
        $kkFile = UploadedFile::fake()->image('kk.jpg', 600, 400);

        // Daftar 2 anggota keluarga dengan tipe kamar 'triple' (promo 43jt per orang)
        $response = $this->actingAs($this->jamaah)->post(route('jamaah.registration.store'), [
            'package_id' => $package->id,
            'package_variant_id' => $variant->id,
            'room_type' => 'triple',
            'members' => [
                [
                    'name' => 'Jamaah Satu',
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
                [
                    'name' => 'Jamaah Dua',
                    'birth_place' => 'Bandung',
                    'birth_date' => '1992-02-02',
                    'gender' => 'perempuan',
                    'nik' => '3273010202920002',
                    'address' => 'Jl. Merdeka No 1',
                    'no_kk' => '3273010101900000',
                    'relationship' => 'istri',
                    'ktp_file' => $ktpFile,
                    'kk_file' => $kkFile,
                    'marriage_book_file' => UploadedFile::fake()->image('buku_nikah.jpg', 600, 400),
                ],
            ],
        ]);

        $response->assertRedirect(route('jamaah.my-registration'));

        // Cek pendaftaran tersimpan
        $registration = Registration::where('user_id', $this->jamaah->id)->first();
        $this->assertNotNull($registration);
        $this->assertEquals($variant->id, $registration->package_variant_id);
        $this->assertEquals('triple', $registration->room_type);
        $this->assertCount(2, $registration->members);

        // Total Invoice = 43.000.000 * 2 = 86.000.000
        $this->assertEquals(86000000, (float) $registration->invoice->total_price);

        // Sisa kuota berkurang dari 10 menjadi 8
        $this->assertEquals(8, $variant->fresh()->remaining_quota);
    }
}
