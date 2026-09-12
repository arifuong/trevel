<?php

namespace Tests\Feature;

use App\Helpers\ImageHelper;
use App\Models\Gallery;
use App\Models\Package;
use App\Models\User;
use App\Services\ImageUploadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OnDemandImageDeliveryTest extends TestCase
{
    use RefreshDatabase;

    protected ImageUploadService $imageService;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->imageService = app(ImageUploadService::class);
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    /**
     * Helper untuk membuat file gambar GD nyata.
     */
    protected function createRealImage(int $width = 800, int $height = 600): UploadedFile
    {
        $gd = imagecreatetruecolor($width, $height);
        $bgColor = imagecolorallocate($gd, 30, 80, 50);
        imagefill($gd, 0, 0, $bgColor);

        $tempFile = tempnam(sys_get_temp_dir(), 'test_delivery_');
        imagejpeg($gd, $tempFile, 90);
        imagedestroy($gd);

        return new UploadedFile(
            path: $tempFile,
            originalName: 'sample.jpg',
            mimeType: 'image/jpeg',
            error: null,
            test: true
        );
    }

    public function test_upload_public_photo_saves_only_one_file_without_eager_webp(): void
    {
        $file = $this->createRealImage(1000, 750);

        $path = $this->imageService->uploadPublicPhoto($file, 'packages/photos');

        // File JPG asli harus ada di disk
        Storage::disk('public')->assertExists($path);

        // File WebP companion TIDAK BOLEH dibuat saat upload
        $webpPath = preg_replace('/\.jpg$/i', '.webp', $path);
        Storage::disk('public')->assertMissing($webpPath);
    }

    public function test_upload_document_saves_only_one_file_without_eager_webp(): void
    {
        $file = $this->createRealImage(1500, 1000);

        $path = $this->imageService->uploadDocument($file, 'documents/ktp');

        Storage::disk('public')->assertExists($path);

        $webpPath = preg_replace('/\.jpg$/i', '.webp', $path);
        Storage::disk('public')->assertMissing($webpPath);
    }

    public function test_delivery_route_blocks_path_traversal(): void
    {
        $response = $this->get('/img/../../.env');
        $response->assertStatus(404);

        $response2 = $this->get('/img/packages/../documents/secret.jpg');
        $response2->assertStatus(404);
    }

    public function test_delivery_route_strictly_forbids_access_to_private_documents(): void
    {
        // Simulasi berkas dokumen privat jamaah
        $file = $this->createRealImage(1000, 800);
        $docPath = $this->imageService->uploadDocument($file, 'documents/ktp');
        Storage::disk('public')->assertExists($docPath);

        // Percobaan akses dokumen via rute /img/ wajib ditolak (404)
        $response = $this->get('/img/' . $docPath);
        $response->assertStatus(404);

        // Percobaan akses bukti bayar juga wajib ditolak (404)
        $paymentPath = $this->imageService->uploadDocument($file, 'payments/dp');
        $responsePayment = $this->get('/img/' . $paymentPath);
        $responsePayment->assertStatus(404);
    }

    public function test_delivery_route_converts_to_webp_on_demand_and_caches(): void
    {
        $file = $this->createRealImage(800, 600);
        $photoPath = $this->imageService->uploadPublicPhoto($file, 'packages/photos');

        $cacheWebpRelPath = 'cache/webp/' . $photoPath . '.webp';
        Storage::disk('public')->assertMissing($cacheWebpRelPath);

        // Request 1: Browser mendukung WebP
        $response = $this->withHeaders([
            'Accept' => 'text/html,application/xhtml+xml,image/webp,image/apng,*/*',
        ])->get('/img/' . $photoPath);

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/webp');
        $cacheControl = (string) $response->headers->get('Cache-Control');
        $this->assertStringContainsString('public', $cacheControl);
        $this->assertStringContainsString('max-age=31536000', $cacheControl);
        $this->assertStringContainsString('immutable', $cacheControl);
        $this->assertNotEmpty($response->headers->get('ETag'));
        $this->assertNotEmpty($response->headers->get('Last-Modified'));

        // Pastikan berkas cache WebP ter-generate di disk
        Storage::disk('public')->assertExists($cacheWebpRelPath);

        // Request 2: Browser mengirim request kedua, harus langsung disajikan dari cache
        $response2 = $this->withHeaders([
            'Accept' => 'image/webp,*/*',
        ])->get('/img/' . $photoPath);

        $response2->assertStatus(200);
        $response2->assertHeader('Content-Type', 'image/webp');
    }

    public function test_delivery_route_serves_original_jpg_when_browser_does_not_accept_webp(): void
    {
        $file = $this->createRealImage(800, 600);
        $photoPath = $this->imageService->uploadPublicPhoto($file, 'galleries');

        // Browser lama (hanya menerima jpeg/png, tanpa image/webp)
        $response = $this->withHeaders([
            'Accept' => 'image/jpeg,image/png,*/*',
        ])->get('/img/' . $photoPath);

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/jpeg');

        // Tidak boleh meng-generate cache WebP jika tidak diminta
        $cacheWebpRelPath = 'cache/webp/' . $photoPath . '.webp';
        Storage::disk('public')->assertMissing($cacheWebpRelPath);
    }

    public function test_delivery_route_supports_conditional_get_304_not_modified(): void
    {
        $file = $this->createRealImage(800, 600);
        $photoPath = $this->imageService->uploadPublicPhoto($file, 'packages/photos');

        $firstResponse = $this->withHeaders([
            'Accept' => 'image/webp',
        ])->get('/img/' . $photoPath);

        $firstResponse->assertStatus(200);
        $etag = $firstResponse->headers->get('ETag');
        $this->assertNotEmpty($etag);

        // Request dengan header If-None-Match yang cocok
        $conditionalResponse = $this->withHeaders([
            'Accept' => 'image/webp',
            'If-None-Match' => $etag,
        ])->get('/img/' . $photoPath);

        $conditionalResponse->assertStatus(304);
    }

    public function test_image_helper_and_gallery_thumbnail_url_returns_delivery_route(): void
    {
        $file = $this->createRealImage(800, 600);
        $photoPath = $this->imageService->uploadPublicPhoto($file, 'galleries');

        $gallery = Gallery::create([
            'title' => 'Dokumentasi Umrah Test',
            'type' => 'photo',
            'image_path' => $photoPath,
            'is_active' => true,
        ]);

        $expectedUrl = url('/img/' . $photoPath);
        $this->assertEquals($expectedUrl, $gallery->thumbnail_url);
        $this->assertEquals($expectedUrl, ImageHelper::url($photoPath));
        $this->assertEquals($expectedUrl, img_url($photoPath));
    }

    public function test_deleting_photo_invalidates_cached_webp(): void
    {
        $file = $this->createRealImage(800, 600);
        $photoPath = $this->imageService->uploadPublicPhoto($file, 'packages/photos');

        // Trigger on-demand generation
        $this->withHeaders(['Accept' => 'image/webp'])->get('/img/' . $photoPath);

        $cacheWebpRelPath = 'cache/webp/' . $photoPath . '.webp';
        Storage::disk('public')->assertExists($cacheWebpRelPath);

        // Hapus file via service
        $this->imageService->deleteFile($photoPath);

        Storage::disk('public')->assertMissing($photoPath);
        Storage::disk('public')->assertMissing($cacheWebpRelPath);
    }

    public function test_artisan_clean_command_removes_legacy_webp_files(): void
    {
        // Buat file legacy palsu di storage
        Storage::disk('public')->put('packages/photos/legacy1.webp', 'fake webp content');
        Storage::disk('public')->put('galleries/legacy2.webp', 'fake webp content');

        Storage::disk('public')->assertExists('packages/photos/legacy1.webp');
        Storage::disk('public')->assertExists('galleries/legacy2.webp');

        Artisan::call('images:clean-legacy-webp', ['--force' => true]);

        Storage::disk('public')->assertMissing('packages/photos/legacy1.webp');
        Storage::disk('public')->assertMissing('galleries/legacy2.webp');
    }
}
