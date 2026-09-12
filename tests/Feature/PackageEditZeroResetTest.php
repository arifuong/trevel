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

class PackageEditZeroResetTest extends TestCase
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

    public function test_1_edit_text_and_upload_new_photos_preserves_all_data_together(): void
    {
        $package = Package::create([
            'name' => 'Umroh Awal Tahun',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 9,
            'status' => 'aktif',
            'price' => 0,
            'facilities' => '-',
            'quota' => 30,
        ]);

        $variant = $package->variants()->create([
            'name' => 'VIP Awal',
            'description' => 'Deskripsi lama',
            'quota' => 15,
            'status' => 'aktif',
            'sort_order' => 1,
            'airline_departure' => 'Saudia Airlines',
            'airline_return' => 'Saudia Airlines',
            'hotel_makkah_name' => 'Hotel Makkah Lama',
            'hotel_makkah_star' => 'Bintang 4',
            'hotel_madinah_name' => 'Hotel Madinah Lama',
            'hotel_madinah_star' => 'Bintang 4',
        ]);

        $variant->prices()->create([
            'room_type' => 'quad',
            'normal_price' => 30000000,
            'is_active' => true,
        ]);

        // Simpan 1 foto makkah awal
        $photoOld = $variant->hotelPhotos()->create([
            'hotel_type' => 'makkah',
            'category' => 'main',
            'photo_path' => 'packages/variants/hotels/makkah/old.jpg',
            'sort_order' => 1,
        ]);

        // Admin mengedit SEMUA field teks dan menambahkan foto baru
        $response = $this->actingAs($this->admin)->put(route('admin.packages.variants.update', [$package, $variant]), [
            'name' => 'VIP Ramadhan 2027',
            'description' => 'Paket umroh nyaman, premium, dan lengkap',
            'quota' => 25,
            'status' => 'aktif',
            'sort_order' => 2,
            'airline_departure' => 'Garuda Indonesia',
            'airline_return' => 'Garuda Indonesia',
            'hotel_makkah_name' => 'Pullman Zamzam Makkah Baru',
            'hotel_makkah_star' => 'Bintang 5',
            'hotel_makkah_description' => 'Depan pelataran Masjidil Haram',
            'hotel_madinah_name' => 'Millennium Al Aqiq Baru',
            'hotel_madinah_star' => 'Bintang 5',
            'hotel_madinah_description' => 'Dekat pintu utama Nabawi',
            'prices' => [
                [
                    'room_type' => 'quad',
                    'normal_price' => 35000000,
                    'promo_price' => 33000000,
                    'is_active' => '1',
                ],
            ],
            'hotel_makkah_main_photo' => UploadedFile::fake()->image('makkah_new.jpg', 800, 600),
            'makkah_photos' => [
                UploadedFile::fake()->image('makkah_gallery1.jpg', 800, 600),
            ],
            'includes' => ['Tiket Pesawat PP', 'Visa Umroh'],
            'excludes' => ['Paspor'],
        ]);

        $response->assertRedirect(route('admin.packages.show', $package));

        $variant->refresh();

        // Verifikasi semua teks yang diedit tersimpan
        $this->assertEquals('VIP Ramadhan 2027', $variant->name);
        $this->assertEquals('Paket umroh nyaman, premium, dan lengkap', $variant->description);
        $this->assertEquals(25, $variant->quota);
        $this->assertEquals('Garuda Indonesia', $variant->airline_departure);
        $this->assertEquals('Pullman Zamzam Makkah Baru', $variant->hotel_makkah_name);
        $this->assertEquals('Millennium Al Aqiq Baru', $variant->hotel_madinah_name);

        // Verifikasi harga terupdate
        $price = $variant->prices()->where('room_type', 'quad')->first();
        $this->assertEquals(35000000, (float) $price->normal_price);
        $this->assertEquals(33000000, (float) $price->promo_price);

        // Verifikasi foto lama tetap ada + foto baru ditambahkan
        $makkahPhotos = $variant->hotelPhotos()->where('hotel_type', 'makkah')->get();
        $this->assertCount(3, $makkahPhotos); // 1 lama + 1 main new + 1 gallery new
        $this->assertTrue($makkahPhotos->contains('id', $photoOld->id));
    }

    public function test_2_upload_photos_independently_via_ajax_does_not_modify_other_variant_fields(): void
    {
        $package = Package::create([
            'name' => 'Umroh Reguler',
            'departure_date' => now()->addMonths(3)->format('Y-m-d'),
            'duration' => 12,
            'status' => 'aktif',
            'price' => 0,
            'facilities' => '-',
            'quota' => 40,
        ]);

        $variant = $package->variants()->create([
            'name' => 'Bisnis',
            'description' => 'Deskripsi Asli Bisnis',
            'quota' => 20,
            'status' => 'aktif',
            'hotel_makkah_name' => 'Makkah Hotel',
        ]);

        // Request upload foto saja via AJAX endpoint
        $response = $this->actingAs($this->admin)->postJson(route('admin.packages.variants.upload-photos', [$package, $variant]), [
            'hotel_type' => 'makkah',
            'category' => 'room',
            'photos' => [
                UploadedFile::fake()->image('kamar1.jpg', 800, 600),
                UploadedFile::fake()->image('kamar2.jpg', 800, 600),
            ],
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        // Verifikasi foto bertambah
        $this->assertCount(2, $variant->hotelPhotos()->where('hotel_type', 'makkah')->get());

        // Verifikasi data teks varian TIDAK tersentuh
        $variant->refresh();
        $this->assertEquals('Bisnis', $variant->name);
        $this->assertEquals('Deskripsi Asli Bisnis', $variant->description);
        $this->assertEquals(20, $variant->quota);
        $this->assertEquals('Makkah Hotel', $variant->hotel_makkah_name);
    }

    public function test_3_upload_package_photo_independently_does_not_reset_package_fields(): void
    {
        $package = Package::create([
            'name' => 'Paket Bintang 5',
            'departure_date' => '2027-05-10',
            'duration' => 14,
            'status' => 'aktif',
            'description' => 'Deskripsi paket bintang 5 asli',
            'price' => 45000000,
            'facilities' => '-',
            'quota' => 20,
        ]);

        // Upload main photo saja
        $response = $this->actingAs($this->admin)->postJson(route('admin.packages.upload-photo', $package), [
            'main_photo' => UploadedFile::fake()->image('package_hero.jpg', 1200, 800),
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $package->refresh();
        $this->assertNotNull($package->main_photo);
        $this->assertEquals('Paket Bintang 5', $package->name);
        $this->assertEquals('2027-05-10', $package->departure_date->format('Y-m-d'));
        $this->assertEquals(14, $package->duration);
        $this->assertEquals('Deskripsi paket bintang 5 asli', $package->description);
    }

    public function test_4_deleting_photo_via_deleted_hotel_photo_ids_with_text_updates_succeeds(): void
    {
        $package = Package::create([
            'name' => 'Umroh Syawal',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 9,
            'status' => 'aktif',
            'price' => 0,
            'facilities' => '-',
            'quota' => 30,
        ]);

        $variant = $package->variants()->create([
            'name' => 'Ekonomi',
            'quota' => 20,
            'status' => 'aktif',
        ]);

        $photoKeep = $variant->hotelPhotos()->create([
            'hotel_type' => 'makkah',
            'category' => 'building',
            'photo_path' => 'packages/variants/hotels/makkah/keep.jpg',
            'sort_order' => 1,
        ]);

        $photoDelete = $variant->hotelPhotos()->create([
            'hotel_type' => 'makkah',
            'category' => 'dining',
            'photo_path' => 'packages/variants/hotels/makkah/delete.jpg',
            'sort_order' => 2,
        ]);

        // Update varian: ubah nama + hapus photoDelete via deleted_hotel_photo_ids
        $response = $this->actingAs($this->admin)->put(route('admin.packages.variants.update', [$package, $variant]), [
            'name' => 'Ekonomi Super Saver',
            'quota' => 25,
            'status' => 'aktif',
            'deleted_hotel_photo_ids' => [$photoDelete->id],
        ]);

        $response->assertRedirect(route('admin.packages.show', $package));

        $variant->refresh();
        $this->assertEquals('Ekonomi Super Saver', $variant->name);
        $this->assertEquals(25, $variant->quota);

        // Verifikasi photoDelete telah dihapus dan photoKeep tetap ada
        $this->assertDatabaseMissing('package_variant_hotel_photos', ['id' => $photoDelete->id]);
        $this->assertDatabaseHas('package_variant_hotel_photos', ['id' => $photoKeep->id]);
    }

    public function test_5_editing_text_only_does_not_delete_or_reset_existing_photos(): void
    {
        $package = Package::create([
            'name' => 'Umroh Liburan',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 9,
            'status' => 'aktif',
            'price' => 0,
            'facilities' => '-',
            'quota' => 30,
        ]);

        $variant = $package->variants()->create([
            'name' => 'VIP',
            'quota' => 10,
            'status' => 'aktif',
        ]);

        $photo1 = $variant->hotelPhotos()->create([
            'hotel_type' => 'makkah',
            'category' => 'main',
            'photo_path' => 'packages/variants/hotels/makkah/p1.jpg',
            'sort_order' => 1,
        ]);
        $photo2 = $variant->hotelPhotos()->create([
            'hotel_type' => 'madinah',
            'category' => 'room',
            'photo_path' => 'packages/variants/hotels/madinah/p2.jpg',
            'sort_order' => 1,
        ]);

        // Edit teks saja tanpa upload atau delete foto
        $response = $this->actingAs($this->admin)->put(route('admin.packages.variants.update', [$package, $variant]), [
            'name' => 'VIP Exclusive Gold',
            'quota' => 12,
            'status' => 'aktif',
        ]);

        $response->assertRedirect(route('admin.packages.show', $package));

        $variant->refresh();
        $this->assertEquals('VIP Exclusive Gold', $variant->name);

        // Kedua foto harus tetap utuh
        $this->assertDatabaseHas('package_variant_hotel_photos', ['id' => $photo1->id]);
        $this->assertDatabaseHas('package_variant_hotel_photos', ['id' => $photo2->id]);
    }
}
