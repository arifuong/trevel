<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\PackageVariant;
use App\Models\Registration;
use App\Models\RegistrationMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SafeDeletionTest extends TestCase
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
            'phone_verified_at' => now(),
        ]);

        $this->jamaah = User::factory()->create([
            'role' => 'jamaah',
            'phone_verified_at' => now(),
        ]);
    }

    // ==========================================
    // 1. TEST WAJIB — HAPUS AKUN JAMAAH
    // ==========================================

    public function test_jamaah_test_1_delete_jamaah_without_registrations_deletes_account_safely(): void
    {
        $userToDelete = User::factory()->create([
            'name' => 'Jamaah Baru',
            'role' => 'jamaah',
            'phone_verified_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $userToDelete));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $userToDelete->id]);
    }

    public function test_jamaah_test_2_delete_jamaah_cleans_only_their_files_and_preserves_other_jamaah_files(): void
    {
        // 1. Setup Jamaah A (Target Delete)
        $userA = User::factory()->create([
            'name' => 'Jamaah A',
            'role' => 'jamaah',
            'avatar' => 'avatars/userA.jpg',
        ]);
        Storage::disk('public')->put('avatars/userA.jpg', 'avatar content A');

        $package = Package::create([
            'name' => 'Paket A',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 9,
            'status' => 'aktif',
            'price' => 25000000,
            'facilities' => '-',
            'quota' => 20,
        ]);

        $regA = Registration::create([
            'user_id' => $userA->id,
            'package_id' => $package->id,
            'status' => Registration::STATUS_MENUNGGU_VERIFIKASI_DOKUMEN,
            'total_amount' => 25000000,
        ]);

        $memberA = $regA->members()->create([
            'name' => 'Anggota A',
            'relationship' => 'diri_sendiri',
            'nik' => '1234567890123456',
            'no_kk' => '3201010101010001',
            'ktp_file' => 'documents/userA_ktp.jpg',
            'kk_file' => 'documents/userA_kk.jpg',
            'passport_file' => 'documents/userA_passport.jpg',
        ]);
        Storage::disk('public')->put('documents/userA_ktp.jpg', 'ktp A');
        Storage::disk('public')->put('documents/userA_kk.jpg', 'kk A');
        Storage::disk('public')->put('documents/userA_passport.jpg', 'passport A');

        $paymentA = $regA->payments()->create([
            'type' => 'dp',
            'amount' => 5000000,
            'proof_file' => 'payments/userA_proof.jpg',
            'status' => 'menunggu_verifikasi',
        ]);
        Storage::disk('public')->put('payments/userA_proof.jpg', 'payment proof A');

        // 2. Setup Jamaah B (Harus Tetap Aman dan Tidak Terhapus)
        $userB = User::factory()->create([
            'name' => 'Jamaah B',
            'role' => 'jamaah',
            'avatar' => 'avatars/userB.jpg',
        ]);
        Storage::disk('public')->put('avatars/userB.jpg', 'avatar content B');

        $regB = Registration::create([
            'user_id' => $userB->id,
            'package_id' => $package->id,
            'status' => Registration::STATUS_JAMAAH,
            'total_amount' => 25000000,
        ]);

        $memberB = $regB->members()->create([
            'name' => 'Anggota B',
            'relationship' => 'diri_sendiri',
            'nik' => '6543210987654321',
            'no_kk' => '3201010101010002',
            'ktp_file' => 'documents/userB_ktp.jpg',
        ]);
        Storage::disk('public')->put('documents/userB_ktp.jpg', 'ktp B');

        // 3. Admin menghapus Jamaah A
        $response = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $userA));

        $response->assertRedirect(route('admin.users.index'));

        // 4. Verifikasi Database Jamaah A terhapus
        $this->assertDatabaseMissing('users', ['id' => $userA->id]);
        $this->assertDatabaseMissing('registrations', ['id' => $regA->id]);
        $this->assertDatabaseMissing('registration_members', ['id' => $memberA->id]);
        $this->assertDatabaseMissing('payments', ['id' => $paymentA->id]);

        // 5. Verifikasi File Fisik Jamaah A terhapus
        Storage::disk('public')->assertMissing('avatars/userA.jpg');
        Storage::disk('public')->assertMissing('documents/userA_ktp.jpg');
        Storage::disk('public')->assertMissing('documents/userA_kk.jpg');
        Storage::disk('public')->assertMissing('documents/userA_passport.jpg');
        Storage::disk('public')->assertMissing('payments/userA_proof.jpg');

        // 6. Verifikasi Database & File Fisik Jamaah B TETAP UTUH
        $this->assertDatabaseHas('users', ['id' => $userB->id]);
        $this->assertDatabaseHas('registrations', ['id' => $regB->id]);
        $this->assertDatabaseHas('registration_members', ['id' => $memberB->id]);
        Storage::disk('public')->assertExists('avatars/userB.jpg');
        Storage::disk('public')->assertExists('documents/userB_ktp.jpg');
    }

    public function test_jamaah_test_3_non_admin_cannot_delete_jamaah(): void
    {
        $targetUser = User::factory()->create(['role' => 'jamaah']);

        // Jamaah biasa mencoba menghapus user lain -> diarahkan ke login admin dengan pesan error
        $response = $this->actingAs($this->jamaah)->delete(route('admin.users.destroy', $targetUser));

        $response->assertRedirect(route('admin.login'));
        $response->assertSessionHas('error', 'Anda harus login sebagai admin.');

        $this->assertDatabaseHas('users', ['id' => $targetUser->id]);
    }

    public function test_jamaah_test_4_cannot_delete_admin_account(): void
    {
        $targetAdmin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $targetAdmin));

        $response->assertSessionHas('error', 'Akun Administrator tidak dapat dihapus.');
        $this->assertDatabaseHas('users', ['id' => $targetAdmin->id]);
    }

    // ==========================================
    // 2. TEST WAJIB — HAPUS PAKET UMROH
    // ==========================================

    public function test_package_test_1_delete_unused_package_cleans_dependents_and_photos(): void
    {
        $package = Package::create([
            'name' => 'Paket Umroh Belum Terpakai',
            'departure_date' => now()->addMonths(3)->format('Y-m-d'),
            'duration' => 10,
            'status' => 'aktif',
            'price' => 30000000,
            'facilities' => '-',
            'quota' => 20,
            'main_photo' => 'packages/photos/main.jpg',
        ]);
        Storage::disk('public')->put('packages/photos/main.jpg', 'main photo');

        $variant = $package->variants()->create([
            'name' => 'VIP',
            'main_photo' => 'packages/variants/vip_main.jpg',
            'quota' => 10,
            'status' => 'aktif',
        ]);
        Storage::disk('public')->put('packages/variants/vip_main.jpg', 'vip main photo');

        $hPhoto = $variant->hotelPhotos()->create([
            'hotel_type' => 'makkah',
            'category' => 'main',
            'photo_path' => 'packages/variants/hotels/makkah/hotel1.jpg',
        ]);
        Storage::disk('public')->put('packages/variants/hotels/makkah/hotel1.jpg', 'hotel photo');

        $response = $this->actingAs($this->admin)->delete(route('admin.packages.destroy', $package));

        $response->assertRedirect(route('admin.packages.index'));
        $response->assertSessionHas('success');

        // Verifikasi database bersih
        $this->assertDatabaseMissing('packages', ['id' => $package->id]);
        $this->assertDatabaseMissing('package_variants', ['id' => $variant->id]);
        $this->assertDatabaseMissing('package_variant_hotel_photos', ['id' => $hPhoto->id]);

        // Verifikasi file storage terhapus
        Storage::disk('public')->assertMissing('packages/photos/main.jpg');
        Storage::disk('public')->assertMissing('packages/variants/vip_main.jpg');
        Storage::disk('public')->assertMissing('packages/variants/hotels/makkah/hotel1.jpg');
    }

    public function test_package_test_2_cannot_delete_package_with_active_registrations(): void
    {
        $package = Package::create([
            'name' => 'Paket Umroh Aktif',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 9,
            'status' => 'aktif',
            'price' => 25000000,
            'facilities' => '-',
            'quota' => 20,
        ]);

        $registration = Registration::create([
            'user_id' => $this->jamaah->id,
            'package_id' => $package->id,
            'status' => Registration::STATUS_JAMAAH,
            'total_amount' => 25000000,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.packages.destroy', $package));

        $response->assertSessionHas('error', 'Paket tidak dapat dihapus karena masih digunakan oleh data pendaftaran/transaksi. Nonaktifkan paket jika tidak ingin ditampilkan kepada publik.');

        $this->assertDatabaseHas('packages', ['id' => $package->id]);
    }

    public function test_package_test_3_nonaktif_package_visible_to_admin_hidden_from_public(): void
    {
        $package = Package::create([
            'name' => 'Paket Umroh Nonaktif',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 9,
            'status' => 'nonaktif',
            'price' => 25000000,
            'facilities' => '-',
            'quota' => 20,
        ]);

        // 1. Admin dapat melihat paket nonaktif di panel admin
        $adminResponse = $this->actingAs($this->admin)->get(route('admin.packages.index'));
        $adminResponse->assertOk();
        $adminResponse->assertSee('Paket Umroh Nonaktif');

        // 2. Publik mengakses halaman detail paket nonaktif -> 404
        $publicDetailResponse = $this->get(route('paket.detail', $package->slug));
        $publicDetailResponse->assertNotFound();

        // 3. Publik mengakses halaman katalog paket -> paket nonaktif tidak muncul
        $publicListResponse = $this->get(route('paket'));
        $publicListResponse->assertOk();
        $publicListResponse->assertDontSee('Paket Umroh Nonaktif');
    }

    public function test_package_test_4_delete_package_a_does_not_affect_package_b(): void
    {
        $packageA = Package::create([
            'name' => 'Paket Umroh A',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 9,
            'status' => 'aktif',
            'price' => 25000000,
            'facilities' => '-',
            'quota' => 20,
            'main_photo' => 'packages/photos/pkgA.jpg',
        ]);
        Storage::disk('public')->put('packages/photos/pkgA.jpg', 'photo A');

        $packageB = Package::create([
            'name' => 'Paket Umroh B',
            'departure_date' => now()->addMonths(4)->format('Y-m-d'),
            'duration' => 12,
            'status' => 'aktif',
            'price' => 35000000,
            'facilities' => '-',
            'quota' => 30,
            'main_photo' => 'packages/photos/pkgB.jpg',
        ]);
        Storage::disk('public')->put('packages/photos/pkgB.jpg', 'photo B');

        $this->actingAs($this->admin)->delete(route('admin.packages.destroy', $packageA));

        // Paket A terhapus
        $this->assertDatabaseMissing('packages', ['id' => $packageA->id]);
        Storage::disk('public')->assertMissing('packages/photos/pkgA.jpg');

        // Paket B TETAP UTUH
        $this->assertDatabaseHas('packages', ['id' => $packageB->id]);
        Storage::disk('public')->assertExists('packages/photos/pkgB.jpg');
    }
}
