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

class HotelPhotoPersistenceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Package $package;
    private PackageVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'phone_verified_at' => now(),
        ]);

        $this->package = Package::create([
            'name' => 'Paket Umroh Bintang 5',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 9,
            'status' => 'aktif',
            'price' => 0,
            'facilities' => '-',
            'quota' => 20,
        ]);

        $this->variant = $this->package->variants()->create([
            'name' => 'VIP',
            'quota' => 20,
            'status' => 'aktif',
            'hotel_makkah_name' => 'Pullman Zamzam Makkah',
            'hotel_makkah_star' => 'Bintang 5',
            'hotel_madinah_name' => 'Millennium Al Aqiq',
            'hotel_madinah_star' => 'Bintang 5',
        ]);

        $this->variant->prices()->create([
            'room_type' => 'quad',
            'normal_price' => 35000000,
            'is_active' => true,
        ]);
    }

    public function test_1_adding_a_new_photo_preserves_all_existing_photos(): void
    {
        // 1. Buat 3 foto existing di Hotel Makkah (A, B, C)
        $photoA = $this->variant->hotelPhotos()->create([
            'hotel_type' => 'makkah',
            'photo_path' => 'packages/variants/hotels/makkah/foto_a.jpg',
            'category' => 'main',
            'sort_order' => 1,
        ]);
        $photoB = $this->variant->hotelPhotos()->create([
            'hotel_type' => 'makkah',
            'photo_path' => 'packages/variants/hotels/makkah/foto_b.jpg',
            'category' => 'room',
            'sort_order' => 2,
        ]);
        $photoC = $this->variant->hotelPhotos()->create([
            'hotel_type' => 'makkah',
            'photo_path' => 'packages/variants/hotels/makkah/foto_c.jpg',
            'category' => 'dining',
            'sort_order' => 3,
        ]);

        // Mock files di storage
        Storage::disk('public')->put($photoA->photo_path, 'content A');
        Storage::disk('public')->put($photoB->photo_path, 'content B');
        Storage::disk('public')->put($photoC->photo_path, 'content C');

        // Admin upload foto baru D via building photo
        $photoDFile = UploadedFile::fake()->image('foto_d.jpg', 800, 600);

        $response = $this->actingAs($this->admin)->put(route('admin.packages.variants.update', [$this->package, $this->variant]), [
            'name' => 'VIP',
            'quota' => 20,
            'status' => 'aktif',
            'hotel_makkah_name' => 'Pullman Zamzam Makkah',
            'hotel_makkah_building_photo' => $photoDFile,
            'prices' => [
                ['room_type' => 'quad', 'normal_price' => 35000000, 'is_active' => '1'],
            ],
        ]);

        $response->assertRedirect(route('admin.packages.show', $this->package));

        // Verifikasi: Semua 4 foto (A, B, C, D) tersimpan di database
        $this->assertEquals(4, $this->variant->hotelPhotos()->where('hotel_type', 'makkah')->count());
        $this->assertDatabaseHas('package_variant_hotel_photos', ['id' => $photoA->id]);
        $this->assertDatabaseHas('package_variant_hotel_photos', ['id' => $photoB->id]);
        $this->assertDatabaseHas('package_variant_hotel_photos', ['id' => $photoC->id]);
        $this->assertDatabaseHas('package_variant_hotel_photos', [
            'package_variant_id' => $this->variant->id,
            'hotel_type' => 'makkah',
            'category' => 'building',
        ]);

        // Verifikasi: File fisik A, B, C tidak terhapus
        Storage::disk('public')->assertExists($photoA->photo_path);
        Storage::disk('public')->assertExists($photoB->photo_path);
        Storage::disk('public')->assertExists($photoC->photo_path);
    }

    public function test_2_uploading_multiple_new_photos_appends_to_existing_photos(): void
    {
        // Existing: A, B
        $photoA = $this->variant->hotelPhotos()->create([
            'hotel_type' => 'makkah',
            'photo_path' => 'packages/variants/hotels/makkah/a.jpg',
            'category' => 'main',
            'sort_order' => 1,
        ]);
        $photoB = $this->variant->hotelPhotos()->create([
            'hotel_type' => 'makkah',
            'photo_path' => 'packages/variants/hotels/makkah/b.jpg',
            'category' => 'room',
            'sort_order' => 2,
        ]);

        Storage::disk('public')->put($photoA->photo_path, 'A');
        Storage::disk('public')->put($photoB->photo_path, 'B');

        // Upload: C, D, E via makkah_photos[]
        $files = [
            UploadedFile::fake()->image('c.jpg'),
            UploadedFile::fake()->image('d.jpg'),
            UploadedFile::fake()->image('e.jpg'),
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.packages.variants.update', [$this->package, $this->variant]), [
            'name' => 'VIP',
            'quota' => 20,
            'status' => 'aktif',
            'makkah_photos' => $files,
            'prices' => [
                ['room_type' => 'quad', 'normal_price' => 35000000, 'is_active' => '1'],
            ],
        ]);

        $response->assertRedirect();

        // Total 5 foto (A, B, C, D, E)
        $this->assertEquals(5, $this->variant->hotelPhotos()->where('hotel_type', 'makkah')->count());
        Storage::disk('public')->assertExists($photoA->photo_path);
        Storage::disk('public')->assertExists($photoB->photo_path);
    }

    public function test_3_editing_hotel_data_without_uploading_photos_preserves_all_photos(): void
    {
        // Existing: A, B, C
        $photoA = $this->variant->hotelPhotos()->create(['hotel_type' => 'makkah', 'photo_path' => 'p1.jpg', 'category' => 'main']);
        $photoB = $this->variant->hotelPhotos()->create(['hotel_type' => 'makkah', 'photo_path' => 'p2.jpg', 'category' => 'room']);
        $photoC = $this->variant->hotelPhotos()->create(['hotel_type' => 'makkah', 'photo_path' => 'p3.jpg', 'category' => 'dining']);

        Storage::disk('public')->put('p1.jpg', '1');
        Storage::disk('public')->put('p2.jpg', '2');
        Storage::disk('public')->put('p3.jpg', '3');

        // Admin edit nama hotel & deskripsi tanpa upload foto apapun
        $response = $this->actingAs($this->admin)->put(route('admin.packages.variants.update', [$this->package, $this->variant]), [
            'name' => 'VIP',
            'quota' => 20,
            'status' => 'aktif',
            'hotel_makkah_name' => 'Pullman Zamzam Tower Baru',
            'hotel_makkah_description' => 'Hotel megah tepat di depan pelataran Masjidil Haram.',
            'prices' => [
                ['room_type' => 'quad', 'normal_price' => 35000000, 'is_active' => '1'],
            ],
        ]);

        $response->assertRedirect();

        // 3 foto tetap utuh
        $this->assertEquals(3, $this->variant->hotelPhotos()->where('hotel_type', 'makkah')->count());
        $this->assertDatabaseHas('package_variant_hotel_photos', ['id' => $photoA->id]);
        $this->assertDatabaseHas('package_variant_hotel_photos', ['id' => $photoB->id]);
        $this->assertDatabaseHas('package_variant_hotel_photos', ['id' => $photoC->id]);
        Storage::disk('public')->assertExists('p1.jpg');
        Storage::disk('public')->assertExists('p2.jpg');
        Storage::disk('public')->assertExists('p3.jpg');
    }

    public function test_4_deleting_one_specific_photo_only_removes_that_photo(): void
    {
        $photoA = $this->variant->hotelPhotos()->create(['hotel_type' => 'makkah', 'photo_path' => 'p_a.jpg', 'category' => 'main']);
        $photoB = $this->variant->hotelPhotos()->create(['hotel_type' => 'makkah', 'photo_path' => 'p_b.jpg', 'category' => 'room']);
        $photoC = $this->variant->hotelPhotos()->create(['hotel_type' => 'makkah', 'photo_path' => 'p_c.jpg', 'category' => 'dining']);

        Storage::disk('public')->put('p_a.jpg', 'A');
        Storage::disk('public')->put('p_b.jpg', 'B');
        Storage::disk('public')->put('p_c.jpg', 'C');

        // Admin hapus Foto B
        $response = $this->actingAs($this->admin)->delete(route('admin.packages.variants.hotel-photos.destroy', [
            $this->package,
            $this->variant,
            $photoB
        ]));

        $response->assertRedirect();

        // Sisa Foto A dan C
        $this->assertEquals(2, $this->variant->hotelPhotos()->where('hotel_type', 'makkah')->count());
        $this->assertDatabaseHas('package_variant_hotel_photos', ['id' => $photoA->id]);
        $this->assertDatabaseMissing('package_variant_hotel_photos', ['id' => $photoB->id]);
        $this->assertDatabaseHas('package_variant_hotel_photos', ['id' => $photoC->id]);

        Storage::disk('public')->assertExists('p_a.jpg');
        Storage::disk('public')->assertMissing('p_b.jpg');
        Storage::disk('public')->assertExists('p_c.jpg');
    }

    public function test_5_deleting_one_photo_and_uploading_another_results_in_correct_set(): void
    {
        $photoA = $this->variant->hotelPhotos()->create(['hotel_type' => 'makkah', 'photo_path' => 'p_a.jpg', 'category' => 'main']);
        $photoB = $this->variant->hotelPhotos()->create(['hotel_type' => 'makkah', 'photo_path' => 'p_b.jpg', 'category' => 'room']);
        $photoC = $this->variant->hotelPhotos()->create(['hotel_type' => 'makkah', 'photo_path' => 'p_c.jpg', 'category' => 'dining']);

        Storage::disk('public')->put('p_a.jpg', 'A');
        Storage::disk('public')->put('p_b.jpg', 'B');
        Storage::disk('public')->put('p_c.jpg', 'C');

        // Hapus B via endpoint
        $this->actingAs($this->admin)->delete(route('admin.packages.variants.hotel-photos.destroy', [$this->package, $this->variant, $photoB]));

        // Upload D via update
        $photoDFile = UploadedFile::fake()->image('d.jpg');
        $this->actingAs($this->admin)->put(route('admin.packages.variants.update', [$this->package, $this->variant]), [
            'name' => 'VIP',
            'quota' => 20,
            'status' => 'aktif',
            'hotel_makkah_facility_photo' => $photoDFile,
            'prices' => [
                ['room_type' => 'quad', 'normal_price' => 35000000, 'is_active' => '1'],
            ],
        ]);

        // Hasil: A, C, D (3 foto)
        $this->assertEquals(3, $this->variant->hotelPhotos()->where('hotel_type', 'makkah')->count());
        $this->assertDatabaseHas('package_variant_hotel_photos', ['id' => $photoA->id]);
        $this->assertDatabaseMissing('package_variant_hotel_photos', ['id' => $photoB->id]);
        $this->assertDatabaseHas('package_variant_hotel_photos', ['id' => $photoC->id]);
        $this->assertDatabaseHas('package_variant_hotel_photos', ['category' => 'facility']);
    }

    public function test_6_madinah_hotel_photos_are_also_preserved_and_appended(): void
    {
        $madinahPhoto1 = $this->variant->hotelPhotos()->create(['hotel_type' => 'madinah', 'photo_path' => 'm1.jpg', 'category' => 'main']);
        $madinahPhoto2 = $this->variant->hotelPhotos()->create(['hotel_type' => 'madinah', 'photo_path' => 'm2.jpg', 'category' => 'room']);

        Storage::disk('public')->put('m1.jpg', 'M1');
        Storage::disk('public')->put('m2.jpg', 'M2');

        // Upload madinah new photo via dining
        $newMadinah = UploadedFile::fake()->image('m3.jpg');
        $this->actingAs($this->admin)->put(route('admin.packages.variants.update', [$this->package, $this->variant]), [
            'name' => 'VIP',
            'quota' => 20,
            'status' => 'aktif',
            'hotel_madinah_dining_photo' => $newMadinah,
            'prices' => [
                ['room_type' => 'quad', 'normal_price' => 35000000, 'is_active' => '1'],
            ],
        ]);

        $this->assertEquals(3, $this->variant->hotelPhotos()->where('hotel_type', 'madinah')->count());
        Storage::disk('public')->assertExists('m1.jpg');
        Storage::disk('public')->assertExists('m2.jpg');
    }

    public function test_7_view_reload_displays_all_saved_hotel_photos(): void
    {
        $this->variant->hotelPhotos()->create(['hotel_type' => 'makkah', 'photo_path' => 'view1.jpg', 'category' => 'main']);
        $this->variant->hotelPhotos()->create(['hotel_type' => 'makkah', 'photo_path' => 'view2.jpg', 'category' => 'room']);

        $response = $this->actingAs($this->admin)->get(route('admin.packages.variants.edit', [$this->package, $this->variant]));
        $response->assertOk();
        $response->assertSee('view1.jpg');
        $response->assertSee('view2.jpg');
    }
}
