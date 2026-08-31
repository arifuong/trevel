<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\RegistrationMember;
use App\Models\User;
use App\Services\ImageUploadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Tests\TestCase;

class ImageOptimizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $jamaah;
    protected User $admin;
    protected Package $package;
    protected ImageUploadService $imageService;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->imageService = app(ImageUploadService::class);

        $this->jamaah = User::factory()->create([
            'role' => 'jamaah',
            'phone' => '6281234567890',
            'phone_verified_at' => now(),
        ]);

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'phone' => '6282121483337',
            'phone_verified_at' => now(),
        ]);

        $this->package = Package::create([
            'name' => 'Paket Umrah Reguler 1447H',
            'slug' => 'paket-umrah-reguler-1447h',
            'price' => 30000000,
            'departure_date' => now()->addDays(40),
            'duration' => 9,
            'quota' => 30,
            'facilities' => "Hotel Makkah *5\nHotel Madinah *4\nTiket PP",
            'status' => 'aktif',
        ]);
    }

    /**
     * Helper untuk membuat file gambar GD nyata dengan dimensi dan format tertentu.
     */
    protected function createRealImage(int $width, int $height, string $format = 'jpeg'): UploadedFile
    {
        $gd = imagecreatetruecolor($width, $height);
        $bgColor = imagecolorallocate($gd, rand(50, 200), rand(50, 200), rand(50, 200));
        imagefill($gd, 0, 0, $bgColor);

        // Tambah teks visual ke gambar
        $textColor = imagecolorallocate($gd, 255, 255, 255);
        imagestring($gd, 5, 20, 20, "TEST DOKUMEN ZEIN TOUR {$width}x{$height}", $textColor);

        $tempFile = tempnam(sys_get_temp_dir(), 'test_img_');

        if ($format === 'png') {
            imagepng($gd, $tempFile);
            $filename = 'document.png';
            $mime = 'image/png';
        } elseif ($format === 'webp' && function_exists('imagewebp')) {
            imagewebp($gd, $tempFile);
            $filename = 'document.webp';
            $mime = 'image/webp';
        } else {
            imagejpeg($gd, $tempFile, 95);
            $filename = 'document.jpg';
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
     * 1. Test Jamaah Registration: Upload berkas berdimensi besar (3000x2000px).
     * Pastikan tersimpan dengan dimensi maksimal 2400px dan terkompresi.
     */
    public function test_jamaah_registration_uploads_and_optimizes_all_documents(): void
    {
        $largeKtp = $this->createRealImage(3000, 2000, 'jpeg');
        $largeKk = $this->createRealImage(3200, 2400, 'png');
        $largePassport = $this->createRealImage(2800, 1800, 'jpeg');

        $response = $this->actingAs($this->jamaah)
            ->post(route('jamaah.registration.store'), [
                'package_id' => $this->package->id,
                'members' => [
                    [
                        'name' => 'Ahmad Fauzi',
                        'birth_place' => 'Bandung',
                        'birth_date' => '1990-01-01',
                        'gender' => 'laki-laki',
                        'relationship' => 'diri_sendiri',
                        'nik' => '3201012345670001',
                        'address' => 'Jl. Merdeka No. 45, Bandung',
                        'no_kk' => '3201012345670002',
                        'no_passport' => 'A12345678',
                        'ktp_file' => $largeKtp,
                        'kk_file' => $largeKk,
                        'passport_file' => $largePassport,
                    ]
                ],
            ]);

        $response->assertRedirect(route('jamaah.my-registration'));

        $member = RegistrationMember::first();
        $this->assertNotNull($member);
        $this->assertNotNull($member->ktp_file);
        $this->assertNotNull($member->kk_file);
        $this->assertNotNull($member->passport_file);

        // Pastikan file tersimpan di storage/app/public
        Storage::disk('public')->assertExists($member->ktp_file);
        Storage::disk('public')->assertExists($member->kk_file);
        Storage::disk('public')->assertExists($member->passport_file);

        // Verifikasi nama file adalah UUID dan berekstensi .jpg
        $this->assertMatchesRegularExpression('/^documents\/ktp\/[a-f0-9\-]+\.jpg$/', $member->ktp_file);
        $this->assertMatchesRegularExpression('/^documents\/kk\/[a-f0-9\-]+\.jpg$/', $member->kk_file);
        $this->assertMatchesRegularExpression('/^documents\/passport\/[a-f0-9\-]+\.jpg$/', $member->passport_file);

        // Verifikasi dimensi gambar terkompresi (<= 2400px)
        $manager = new ImageManager(new Driver());
        $ktpBinary = Storage::disk('public')->get($member->ktp_file);
        $decodedKtp = $manager->decodeBinary($ktpBinary);

        $this->assertLessThanOrEqual(2400, $decodedKtp->width());
        $this->assertLessThanOrEqual(2400, $decodedKtp->height());
    }

    /**
     * 2. Test Jamaah Payment: Upload bukti DP dan bukti Pelunasan.
     */
    public function test_jamaah_payment_dp_and_pelunasan_uploads_and_optimizes(): void
    {
        $registration = Registration::create([
            'user_id' => $this->jamaah->id,
            'package_id' => $this->package->id,
            'status' => Registration::STATUS_MENUNGGU_PEMBAYARAN_DP,
        ]);

        RegistrationMember::create([
            'registration_id' => $registration->id,
            'name' => 'Ahmad Fauzi',
            'relationship' => 'diri_sendiri',
            'nik' => '3201012345670001',
            'no_kk' => '3201012345670002',
            'document_status' => 'disetujui',
        ]);

        Invoice::create([
            'registration_id' => $registration->id,
            'total_price' => 30000000,
            'total_paid' => 0,
            'remaining_balance' => 30000000,
            'due_date' => now()->addDays(7),
        ]);

        // Upload DP
        $dpReceipt = $this->createRealImage(2600, 3400, 'jpeg');

        $dpResponse = $this->actingAs($this->jamaah)
            ->post(route('jamaah.payment.dp.store'), [
                'amount' => 5000000,
                'proof_file' => $dpReceipt,
            ]);

        $dpResponse->assertRedirect(route('jamaah.my-registration'));

        $dpPayment = Payment::where('type', Payment::TYPE_DP)->first();
        $this->assertNotNull($dpPayment);
        Storage::disk('public')->assertExists($dpPayment->proof_file);
        $this->assertMatchesRegularExpression('/^payments\/dp\/[a-f0-9\-]+\.jpg$/', $dpPayment->proof_file);

        // Simulasi Admin approve DP
        $dpPayment->update(['status' => Payment::STATUS_DISETUJUI]);
        $registration->update(['status' => Registration::STATUS_JAMAAH]);
        $registration->invoice->update(['total_paid' => 5000000, 'remaining_balance' => 25000000]);

        // Upload Pelunasan
        $pelunasanReceipt = $this->createRealImage(2000, 2000, 'png');

        $pelunasanResponse = $this->actingAs($this->jamaah)
            ->post(route('jamaah.payment.pelunasan.store'), [
                'amount' => 10000000,
                'proof_file' => $pelunasanReceipt,
            ]);

        $pelunasanResponse->assertRedirect(route('jamaah.my-registration'));

        $pelunasanPayment = Payment::where('type', Payment::TYPE_PELUNASAN)->first();
        $this->assertNotNull($pelunasanPayment);
        Storage::disk('public')->assertExists($pelunasanPayment->proof_file);
        $this->assertMatchesRegularExpression('/^payments\/pelunasan\/[a-f0-9\-]+\.jpg$/', $pelunasanPayment->proof_file);
    }

    /**
     * 3. Test upload file non-gambar ditolak oleh validasi.
     */
    public function test_non_image_file_is_rejected(): void
    {
        $fakeTxt = UploadedFile::fake()->create('malicious.txt', 100, 'text/plain');

        $response = $this->actingAs($this->jamaah)
            ->post(route('jamaah.payment.dp.store'), [
                'amount' => 5000000,
                'proof_file' => $fakeTxt,
            ]);

        $response->assertSessionHasErrors('proof_file');
    }

    /**
     * 4. Test upload file > 10MB ditolak oleh validasi.
     */
    public function test_file_larger_than_10mb_is_rejected(): void
    {
        $oversized = UploadedFile::fake()->create('huge.jpg', 12000, 'image/jpeg');

        $response = $this->actingAs($this->jamaah)
            ->post(route('jamaah.payment.dp.store'), [
                'amount' => 5000000,
                'proof_file' => $oversized,
            ]);

        $response->assertSessionHasErrors('proof_file');
    }

    /**
     * 5. Test Penggantian Dokumen (Replace File):
     * File baru berhasil diupload & dikompres, file lama otomatis terhapus dari storage.
     */
    public function test_document_replacement_deletes_old_file(): void
    {
        $initialKtp = $this->createRealImage(1500, 1000, 'jpeg');
        $oldPath = $this->imageService->uploadDocument($initialKtp, 'documents/ktp');

        Storage::disk('public')->assertExists($oldPath);

        $member = RegistrationMember::create([
            'registration_id' => Registration::create([
                'user_id' => $this->jamaah->id,
                'package_id' => $this->package->id,
                'status' => Registration::STATUS_MENUNGGU_VERIFIKASI_DOKUMEN,
            ])->id,
            'name' => 'Ahmad Fauzi',
            'relationship' => 'diri_sendiri',
            'nik' => '3201012345670001',
            'no_kk' => '3201012345670002',
            'ktp_file' => $oldPath,
            'document_status' => 'ditolak',
            'rejection_reason' => 'Foto KTP buram',
        ]);

        // Ganti dokumen dengan file baru lengkap (Full Resubmission)
        $newKtp = $this->createRealImage(2200, 1500, 'jpeg');
        $newKk = $this->createRealImage(2200, 1500, 'jpeg');
        $newPassport = $this->createRealImage(2200, 1500, 'jpeg');

        $replaceResponse = $this->actingAs($this->jamaah)
            ->post(route('jamaah.members.documents.update', $member), [
                'name' => 'Ahmad Fauzi',
                'birth_place' => 'Bandung',
                'birth_date' => '1990-01-01',
                'gender' => 'laki-laki',
                'nik' => '3201012345670001',
                'address' => 'Jl. Merdeka No. 45, Bandung',
                'no_kk' => '3201012345670002',
                'relationship' => 'diri_sendiri',
                'ktp_file' => $newKtp,
                'kk_file' => $newKk,
                'passport_file' => $newPassport,
            ]);

        $replaceResponse->assertRedirect();

        $member->refresh();

        // Path baru tersimpan
        $this->assertNotEquals($oldPath, $member->ktp_file);
        Storage::disk('public')->assertExists($member->ktp_file);

        // File LAMA harus sudah terhapus dari storage
        Storage::disk('public')->assertMissing($oldPath);

        // Status dokumen kembali menjadi menunggu_verifikasi
        $this->assertEquals('menunggu_verifikasi', $member->document_status);
        $this->assertNull($member->rejection_reason);
    }

    /**
     * 6. Test Admin Penggantian Dokumen Jamaah:
     * Admin mengganti dokumen jamaah -> file lama terhapus, file baru dikompres.
     */
    public function test_admin_can_replace_and_delete_member_documents(): void
    {
        $initialKtp = $this->createRealImage(1500, 1000, 'jpeg');
        $oldPath = $this->imageService->uploadDocument($initialKtp, 'documents/ktp');

        $member = RegistrationMember::create([
            'registration_id' => Registration::create([
                'user_id' => $this->jamaah->id,
                'package_id' => $this->package->id,
                'status' => Registration::STATUS_MENUNGGU_VERIFIKASI_DOKUMEN,
            ])->id,
            'name' => 'Ahmad Fauzi',
            'relationship' => 'diri_sendiri',
            'nik' => '3201012345670001',
            'no_kk' => '3201012345670002',
            'ktp_file' => $oldPath,
        ]);

        $adminNewKtp = $this->createRealImage(2500, 1800, 'png');

        $adminResponse = $this->actingAs($this->admin)
            ->post(route('admin.members.documents.update', $member), [
                'document_type' => 'ktp_file',
                'file' => $adminNewKtp,
            ]);

        $adminResponse->assertRedirect();

        $member->refresh();
        $this->assertNotEquals($oldPath, $member->ktp_file);
        Storage::disk('public')->assertExists($member->ktp_file);
        Storage::disk('public')->assertMissing($oldPath);

        // Admin menghapus dokumen
        $deleteDocResponse = $this->actingAs($this->admin)
            ->delete(route('admin.members.documents.delete', ['member' => $member, 'documentType' => 'ktp_file']));

        $deleteDocResponse->assertRedirect();

        $newPath = $member->ktp_file;
        $member->refresh();
        $this->assertNull($member->ktp_file);
        Storage::disk('public')->assertMissing($newPath);
    }

    /**
     * 7. Test Penghapusan Record Menghapus File Fisik (No Orphan Files).
     */
    public function test_deleting_member_or_payment_cleans_up_physical_storage_files(): void
    {
        $ktpFile = $this->createRealImage(1500, 1000, 'jpeg');
        $kkFile = $this->createRealImage(1500, 1000, 'jpeg');
        $proofFile = $this->createRealImage(1500, 1000, 'jpeg');

        $ktpPath = $this->imageService->uploadDocument($ktpFile, 'documents/ktp');
        $kkPath = $this->imageService->uploadDocument($kkFile, 'documents/kk');
        $proofPath = $this->imageService->uploadDocument($proofFile, 'payments/dp');

        Storage::disk('public')->assertExists($ktpPath);
        Storage::disk('public')->assertExists($kkPath);
        Storage::disk('public')->assertExists($proofPath);

        $registration = Registration::create([
            'user_id' => $this->jamaah->id,
            'package_id' => $this->package->id,
            'status' => Registration::STATUS_MENUNGGU_VERIFIKASI_DOKUMEN,
        ]);

        $member = RegistrationMember::create([
            'registration_id' => $registration->id,
            'name' => 'Ahmad Fauzi',
            'relationship' => 'diri_sendiri',
            'nik' => '3201012345670001',
            'no_kk' => '3201012345670002',
            'ktp_file' => $ktpPath,
            'kk_file' => $kkPath,
        ]);

        $payment = Payment::create([
            'registration_id' => $registration->id,
            'type' => 'dp',
            'amount' => 5000000,
            'proof_file' => $proofPath,
        ]);

        // Hapus registration (cascade ke member & payment)
        $registration->delete();

        // Seluruh file fisik harus terhapus dari storage
        Storage::disk('public')->assertMissing($ktpPath);
        Storage::disk('public')->assertMissing($kkPath);
        Storage::disk('public')->assertMissing($proofPath);
    }
}
