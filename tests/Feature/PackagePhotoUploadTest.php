<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\PackageVariant;
use App\Models\PackageVariantHotelPhoto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PackagePhotoUploadTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);
    }

    public function test_1_admin_can_create_parent_package_with_main_photo(): void
    {
        $mainPhoto = UploadedFile::fake()->image('parent_main.jpg', 800, 600);

        $response = $this->actingAs($this->admin)->post(route('admin.packages.store'), [
            'name' => 'PAKET UMROH SPESIAL 12 HARI',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 12,
            'description' => 'Paket umroh 12 hari.',
            'status' => 'aktif',
            'package_type' => 'umrah',
            'main_photo' => $mainPhoto,
        ]);

        $response->assertRedirect();

        $package = Package::where('name', 'PAKET UMROH SPESIAL 12 HARI')->first();
        $this->assertNotNull($package);
        $this->assertNotNull($package->main_photo);

        // Verify storage files exist
        Storage::disk('public')->assertExists($package->main_photo);
    }

    public function test_admin_cannot_upload_pdf_for_packages(): void
    {
        $pdfFile = UploadedFile::fake()->create('document.pdf', 1024, 'application/pdf');

        $response = $this->actingAs($this->admin)->post(route('admin.packages.store'), [
            'name' => 'PAKET UMROH SPESIAL',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 12,
            'status' => 'aktif',
            'package_type' => 'umrah',
            'main_photo' => $pdfFile,
        ]);

        $response->assertSessionHasErrors('main_photo');
    }

    public function test_2_and_3_admin_can_create_variant_with_photos_and_airline_logos(): void
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

        $variantPhoto = UploadedFile::fake()->image('vip_main.jpg', 800, 600);
        $depLogo = UploadedFile::fake()->image('saudia_logo.png', 200, 100);
        $retLogo = UploadedFile::fake()->image('garuda_logo.png', 200, 100);

        $response = $this->actingAs($this->admin)->post(route('admin.packages.variants.store', $package), [
            'name' => 'VIP',
            'description' => 'Kelas VIP Bintang 5',
            'quota' => 15,
            'status' => 'aktif',
            'main_photo' => $variantPhoto,
            'airline_departure' => 'Saudia Airlines Direct',
            'airline_departure_logo' => $depLogo,
            'airline_return' => 'Garuda Indonesia Direct',
            'airline_return_logo' => $retLogo,
            'prices' => [
                ['room_type' => 'quad', 'normal_price' => 42000000, 'promo_price' => 40000000, 'is_active' => '1'],
            ],
        ]);

        $response->assertRedirect(route('admin.packages.show', $package));

        $variant = PackageVariant::where('package_id', $package->id)->where('name', 'VIP')->first();
        $this->assertNotNull($variant);

        // Verify storage files
        Storage::disk('public')->assertExists($variant->main_photo);
        Storage::disk('public')->assertExists($variant->airline_departure_logo);
        Storage::disk('public')->assertExists($variant->airline_return_logo);
    }

    public function test_4_and_5_admin_can_upload_categorized_makkah_and_madinah_hotel_photos_without_collision(): void
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

        $response = $this->actingAs($this->admin)->post(route('admin.packages.variants.store', $package), [
            'name' => 'VIP',
            'quota' => 15,
            'status' => 'aktif',
            'hotel_makkah_name' => 'Dar Al Eiman Royal',
            'hotel_makkah_star' => 'Bintang 5',
            'hotel_makkah_main_photo' => UploadedFile::fake()->image('makkah_main.jpg'),
            'hotel_makkah_building_photo' => UploadedFile::fake()->image('makkah_bldg.jpg'),
            'hotel_makkah_room_photo' => UploadedFile::fake()->image('makkah_room.jpg'),
            'hotel_makkah_dining_photo' => UploadedFile::fake()->image('makkah_dining.jpg'),
            'hotel_makkah_facility_photo' => UploadedFile::fake()->image('makkah_fac.jpg'),
            'hotel_madinah_name' => 'Millennium Al Aqiq',
            'hotel_madinah_star' => 'Bintang 5',
            'hotel_madinah_main_photo' => UploadedFile::fake()->image('madinah_main.jpg'),
            'hotel_madinah_room_photo' => UploadedFile::fake()->image('madinah_room.jpg'),
        ]);

        $response->assertRedirect(route('admin.packages.show', $package));

        $variant = PackageVariant::where('package_id', $package->id)->where('name', 'VIP')->first();
        $this->assertNotNull($variant);

        $makkahPhotos = $variant->hotelPhotos()->where('hotel_type', 'makkah')->get();
        $madinahPhotos = $variant->hotelPhotos()->where('hotel_type', 'madinah')->get();

        $this->assertCount(5, $makkahPhotos);
        $this->assertCount(2, $madinahPhotos);

        foreach ($makkahPhotos as $photo) {
            Storage::disk('public')->assertExists($photo->photo_path);
            $this->assertEquals('makkah', $photo->hotel_type);
        }

        foreach ($madinahPhotos as $photo) {
            Storage::disk('public')->assertExists($photo->photo_path);
            $this->assertEquals('madinah', $photo->hotel_type);
        }
    }

    public function test_6_editing_variant_replaces_main_photo_and_deletes_old_file(): void
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

        $oldPhoto = UploadedFile::fake()->image('old_main.jpg')->store('packages/variants/photos', 'public');

        $variant = $package->variants()->create([
            'name' => 'VIP',
            'slug' => 'vip-12h',
            'quota' => 10,
            'status' => 'aktif',
            'main_photo' => $oldPhoto,
        ]);

        Storage::disk('public')->assertExists($oldPhoto);

        $newPhoto = UploadedFile::fake()->image('new_main.jpg');

        $response = $this->actingAs($this->admin)->put(route('admin.packages.variants.update', [$package, $variant]), [
            'name' => 'VIP',
            'quota' => 10,
            'status' => 'aktif',
            'main_photo' => $newPhoto,
        ]);

        $response->assertRedirect(route('admin.packages.show', $package));

        $variant->refresh();

        // Old file must be removed from storage
        Storage::disk('public')->assertMissing($oldPhoto);
        // New file must exist
        Storage::disk('public')->assertExists($variant->main_photo);
        $this->assertNotEquals($oldPhoto, $variant->main_photo);
    }

    public function test_7_admin_can_delete_individual_hotel_photo(): void
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

        $variant = $package->variants()->create([
            'name' => 'VIP',
            'slug' => 'vip-12h',
            'quota' => 10,
            'status' => 'aktif',
        ]);

        $hotelPath = UploadedFile::fake()->image('hotel.jpg')->store('packages/variants/hotels/makkah', 'public');
        $hotelPhoto = $variant->hotelPhotos()->create(['hotel_type' => 'makkah', 'photo_path' => $hotelPath, 'category' => 'room']);

        Storage::disk('public')->assertExists($hotelPath);

        // Delete hotel photo
        $delHotelResponse = $this->actingAs($this->admin)->delete(route('admin.packages.variants.hotel-photos.destroy', [$package, $variant, $hotelPhoto]));
        $delHotelResponse->assertRedirect();
        Storage::disk('public')->assertMissing($hotelPath);
        $this->assertDatabaseMissing('package_variant_hotel_photos', ['id' => $hotelPhoto->id]);
    }
}
