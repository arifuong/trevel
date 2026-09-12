<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\ImageUploadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Tests\TestCase;

class JamaahProfileTest extends TestCase
{
    use RefreshDatabase;

    protected User $jamaah;
    protected ImageUploadService $imageService;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->imageService = app(ImageUploadService::class);

        $this->jamaah = User::factory()->create([
            'name' => 'Muhammad Arif Hasbi',
            'email' => 'arifhasbi56@gmail.com',
            'phone' => '6281234567890',
            'role' => 'jamaah',
            'password' => Hash::make('password123'),
        ]);
    }

    protected function createRealImage(int $width, int $height, string $format = 'jpeg'): UploadedFile
    {
        $gd = imagecreatetruecolor($width, $height);
        $bgColor = imagecolorallocate($gd, 40, 100, 70);
        imagefill($gd, 0, 0, $bgColor);
        $textColor = imagecolorallocate($gd, 255, 255, 255);
        imagestring($gd, 5, 20, 20, "AVATAR TEST {$width}x{$height}", $textColor);

        $tempFile = tempnam(sys_get_temp_dir(), 'test_avatar_');

        if ($format === 'png') {
            imagepng($gd, $tempFile);
            $filename = 'avatar.png';
            $mime = 'image/png';
        } else {
            imagejpeg($gd, $tempFile, 90);
            $filename = 'avatar.jpg';
            $mime = 'image/jpeg';
        }

        imagedestroy($gd);

        return new UploadedFile(
            path: $tempFile,
            originalName: $filename,
            mimeType: $mime,
            error: null,
            test: true
        );
    }

    /**
     * 1. Test Jamaah dapat melihat halaman profil (/jamaah/profil dan /profil).
     * Pastikan ID Jamaah / ID internal tidak ditampilkan ke user.
     */
    public function test_jamaah_can_view_profile_page_without_internal_ids(): void
    {
        $response = $this->actingAs($this->jamaah)->get(route('jamaah.profile'));

        $response->assertOk();
        $response->assertSee('Muhammad Arif Hasbi');
        $response->assertSee('arifhasbi56@gmail.com');
        $response->assertSee('+62 812-3456-7890');
        $response->assertSee('Informasi Pribadi');
        $response->assertSee('Keamanan Akun');

        // Pastikan TIDAK ADA tulisan "ID Jamaah: #..." atau format ID serupa
        $response->assertDontSee('ID Jamaah:');
        $response->assertDontSee('ID Jamaah');
        $response->assertDontSee('#' . str_pad($this->jamaah->id, 5, '0', STR_PAD_LEFT));
    }

    /**
     * 2. Test Guest diarahkan ke login jika mengakses profil.
     */
    public function test_guest_cannot_access_profile_page(): void
    {
        $response = $this->get(route('jamaah.profile'));
        $response->assertRedirect(route('login'));
    }

    /**
     * 3. Test Jamaah dapat memperbarui informasi pribadi.
     */
    public function test_jamaah_can_update_personal_information(): void
    {
        $response = $this->actingAs($this->jamaah)
            ->put(route('jamaah.profile.update'), [
                'name' => 'Muhammad Arif Hasbi Al-Faruq',
                'phone' => '081299988877',
                'gender' => 'laki-laki',
                'birth_place' => 'Bandung',
                'birth_date' => '1995-05-15',
                'address' => 'Jl. Buah Batu No. 45, Kota Bandung',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', '✓ Profil berhasil diperbarui.');

        $this->jamaah->refresh();
        $this->assertEquals('Muhammad Arif Hasbi Al-Faruq', $this->jamaah->name);
        $this->assertEquals('6281299988877', $this->jamaah->phone);
        $this->assertEquals('+62 812-9998-8877', $this->jamaah->phone_formatted);
        $this->assertEquals('laki-laki', $this->jamaah->gender);
        $this->assertEquals('Bandung', $this->jamaah->birth_place);
        $this->assertEquals('1995-05-15', $this->jamaah->birth_date->format('Y-m-d'));
        $this->assertEquals('Jl. Buah Batu No. 45, Kota Bandung', $this->jamaah->address);
    }

    /**
     * 4. Test Jamaah dapat mengunggah dan mengompres foto profil.
     */
    public function test_jamaah_can_upload_and_compress_profile_photo(): void
    {
        $largePhoto = $this->createRealImage(2600, 2600, 'jpeg');

        $response = $this->actingAs($this->jamaah)
            ->post(route('jamaah.profile.photo.update'), [
                'avatar' => $largePhoto,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', '✓ Foto profil berhasil diperbarui.');

        $this->jamaah->refresh();
        $this->assertNotNull($this->jamaah->avatar);
        Storage::disk('public')->assertExists($this->jamaah->avatar);
        $this->assertMatchesRegularExpression('/^avatars\/[a-f0-9\-]+\.jpg$/', $this->jamaah->avatar);

        // Verifikasi dimensi foto profil di-scaleDown <= 1800px
        $manager = new ImageManager(new Driver());
        $photoBinary = Storage::disk('public')->get($this->jamaah->avatar);
        $decoded = $manager->decodeBinary($photoBinary);

        $this->assertLessThanOrEqual(1800, $decoded->width());
        $this->assertLessThanOrEqual(1800, $decoded->height());
    }

    /**
     * 5. Test Penggantian Foto Profil: File lama otomatis dihapus dari storage.
     */
    public function test_replacing_profile_photo_deletes_old_file(): void
    {
        $initialPhoto = $this->createRealImage(1000, 1000, 'jpeg');
        $oldPath = $this->imageService->uploadPhoto($initialPhoto, 'avatars');

        $this->jamaah->update(['avatar' => $oldPath]);
        Storage::disk('public')->assertExists($oldPath);

        // Upload foto baru
        $newPhoto = $this->createRealImage(1200, 1200, 'png');
        $response = $this->actingAs($this->jamaah)
            ->post(route('jamaah.profile.photo.update'), [
                'avatar' => $newPhoto,
            ]);

        $response->assertRedirect();
        $this->jamaah->refresh();

        $this->assertNotEquals($oldPath, $this->jamaah->avatar);
        Storage::disk('public')->assertExists($this->jamaah->avatar);
        Storage::disk('public')->assertMissing($oldPath);
    }

    /**
     * 6. Test Jamaah dapat menghapus foto profil.
     */
    public function test_jamaah_can_delete_profile_photo(): void
    {
        $photo = $this->createRealImage(1000, 1000, 'jpeg');
        $path = $this->imageService->uploadPhoto($photo, 'avatars');
        $this->jamaah->update(['avatar' => $path]);

        Storage::disk('public')->assertExists($path);

        $response = $this->actingAs($this->jamaah)
            ->delete(route('jamaah.profile.photo.delete'));

        $response->assertRedirect();
        $response->assertSessionHas('success', '✓ Foto profil berhasil dihapus.');

        $this->jamaah->refresh();
        $this->assertNull($this->jamaah->avatar);
        Storage::disk('public')->assertMissing($path);
    }

    /**
     * 7. Test Jamaah dapat memperbarui kata sandi.
     */
    public function test_jamaah_can_update_password(): void
    {
        $response = $this->actingAs($this->jamaah)
            ->put(route('jamaah.profile.password.update'), [
                'current_password' => 'password123',
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', '✓ Kata sandi berhasil diperbarui.');

        $this->jamaah->refresh();
        $this->assertTrue(Hash::check('newpassword123', $this->jamaah->password));
    }

    /**
     * 8. Test Gagal update password jika kata sandi saat ini salah.
     */
    public function test_cannot_update_password_with_wrong_current_password(): void
    {
        $response = $this->actingAs($this->jamaah)
            ->put(route('jamaah.profile.password.update'), [
                'current_password' => 'passwordsalah',
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ]);

        $response->assertSessionHasErrors('current_password');
    }

    /**
     * 9. Test file non-image ditolak saat upload foto profil.
     */
    public function test_non_image_file_is_rejected_for_avatar(): void
    {
        $fakeTxt = UploadedFile::fake()->create('malicious.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->jamaah)
            ->post(route('jamaah.profile.photo.update'), [
                'avatar' => $fakeTxt,
            ]);

        $response->assertSessionHasErrors('avatar');
    }
}
