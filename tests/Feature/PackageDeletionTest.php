<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\PackageVariant;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PackageDeletionTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'phone_verified_at' => now(),
        ]);
    }

    public function test_admin_can_view_packages_index_with_delete_buttons_and_modal(): void
    {
        $package = Package::create([
            'name' => 'PAKET UMROH SPESIAL 12 HARI',
            'slug' => 'paket-umroh-spesial-12-hari',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 12,
            'status' => 'aktif',
            'price' => 0,
            'facilities' => '-',
            'quota' => 0,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.packages.index'));

        $response->assertStatus(200);
        $response->assertSee('PAKET UMROH SPESIAL 12 HARI');
        $response->assertSee('openDeleteModal', false);
        $response->assertSee('Hapus', false);
        $response->assertSee('Hapus Paket Induk', false);
    }

    public function test_1_and_2_admin_can_delete_package_with_variants_photos_and_clean_all_files(): void
    {
        $packagePhoto = UploadedFile::fake()->image('pkg_main.jpg')->store('packages/photos', 'public');

        $package = Package::create([
            'name' => 'PAKET UMROH SPESIAL 12 HARI',
            'slug' => 'paket-umroh-spesial-12-hari',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 12,
            'status' => 'aktif',
            'price' => 0,
            'facilities' => '-',
            'quota' => 0,
            'main_photo' => $packagePhoto,
        ]);

        $variantPhoto = UploadedFile::fake()->image('var_main.jpg')->store('packages/variants/photos', 'public');
        $depLogo = UploadedFile::fake()->image('saudia.png')->store('packages/variants/airlines', 'public');
        $retLogo = UploadedFile::fake()->image('garuda.png')->store('packages/variants/airlines', 'public');
        $hotelPhotoPath = UploadedFile::fake()->image('hotel.jpg')->store('packages/variants/hotels/makkah', 'public');

        $variant = $package->variants()->create([
            'name' => 'VIP',
            'slug' => 'vip-12h',
            'quota' => 10,
            'status' => 'aktif',
            'main_photo' => $variantPhoto,
            'airline_departure_logo' => $depLogo,
            'airline_return_logo' => $retLogo,
        ]);

        $vPrice = $variant->prices()->create([
            'room_type' => 'quad',
            'normal_price' => 45000000,
            'is_active' => true,
        ]);

        $hPhoto = $variant->hotelPhotos()->create(['hotel_type' => 'makkah', 'photo_path' => $hotelPhotoPath, 'category' => 'main']);
        $vInc = $variant->includes()->create(['item' => 'Tiket PP']);
        $vExc = $variant->excludes()->create(['item' => 'Paspor']);

        // Assert files exist before delete
        Storage::disk('public')->assertExists($packagePhoto);
        Storage::disk('public')->assertExists($variantPhoto);
        Storage::disk('public')->assertExists($depLogo);
        Storage::disk('public')->assertExists($retLogo);
        Storage::disk('public')->assertExists($hotelPhotoPath);

        // Delete package
        $response = $this->actingAs($this->admin)->delete(route('admin.packages.destroy', $package));

        $response->assertRedirect(route('admin.packages.index'));
        $response->assertSessionHas('success');

        // Assert DB records deleted
        $this->assertDatabaseMissing('packages', ['id' => $package->id]);
        $this->assertDatabaseMissing('package_variants', ['id' => $variant->id]);
        $this->assertDatabaseMissing('package_variant_prices', ['id' => $vPrice->id]);
        $this->assertDatabaseMissing('package_variant_hotel_photos', ['id' => $hPhoto->id]);
        $this->assertDatabaseMissing('package_variant_includes', ['id' => $vInc->id]);
        $this->assertDatabaseMissing('package_variant_excludes', ['id' => $vExc->id]);

        // Assert all physical files deleted from storage
        Storage::disk('public')->assertMissing($packagePhoto);
        Storage::disk('public')->assertMissing($variantPhoto);
        Storage::disk('public')->assertMissing($depLogo);
        Storage::disk('public')->assertMissing($retLogo);
        Storage::disk('public')->assertMissing($hotelPhotoPath);
    }

    public function test_3_delete_rejected_if_package_or_variant_has_active_registrations(): void
    {
        $package = Package::create([
            'name' => 'PAKET UMROH SPESIAL 12 HARI',
            'slug' => 'paket-umroh-spesial-12-hari',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 12,
            'status' => 'aktif',
            'price' => 0,
            'facilities' => '-',
            'quota' => 10,
        ]);

        $variant = $package->variants()->create([
            'name' => 'VIP',
            'slug' => 'vip-12h',
            'quota' => 10,
            'status' => 'aktif',
        ]);

        $user = User::factory()->create(['role' => 'jamaah']);

        // Create active registration
        Registration::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'package_variant_id' => $variant->id,
            'room_type' => 'quad',
            'status' => Registration::STATUS_MENUNGGU_VERIFIKASI_DOKUMEN,
            'registration_number' => 'REG-ACTV01',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.packages.destroy', $package));

        $response->assertSessionHas('error', 'Paket tidak dapat dihapus karena masih digunakan oleh data pendaftaran/transaksi. Nonaktifkan paket jika tidak ingin ditampilkan kepada publik.');

        // Assert package & variant still exist
        $this->assertDatabaseHas('packages', ['id' => $package->id]);
        $this->assertDatabaseHas('package_variants', ['id' => $variant->id]);
    }

    public function test_4_delete_allowed_if_all_registrations_are_cancelled(): void
    {
        $package = Package::create([
            'name' => 'PAKET UMROH SPESIAL 12 HARI',
            'slug' => 'paket-umroh-spesial-12-hari',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 12,
            'status' => 'aktif',
            'price' => 0,
            'facilities' => '-',
            'quota' => 10,
        ]);

        $variant = $package->variants()->create([
            'name' => 'VIP',
            'slug' => 'vip-12h',
            'quota' => 10,
            'status' => 'aktif',
        ]);

        $user = User::factory()->create(['role' => 'jamaah']);

        // Create cancelled registration
        Registration::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'package_variant_id' => $variant->id,
            'room_type' => 'quad',
            'status' => Registration::STATUS_DIBATALKAN,
            'registration_number' => 'REG-CNCL01',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.packages.destroy', $package));

        $response->assertRedirect(route('admin.packages.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('packages', ['id' => $package->id]);
        $this->assertDatabaseMissing('package_variants', ['id' => $variant->id]);
    }
}
