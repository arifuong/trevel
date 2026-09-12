<?php

namespace Tests\Feature;

use App\Models\Gallery;
use App\Models\User;
use Database\Seeders\GallerySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GalleryManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->seed(GallerySeeder::class);
    }

    /**
     * 1. Verifikasi Pembatalan Admin Settings (Tidak Boleh Ada)
     */
    public function test_admin_settings_page_does_not_exist(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/settings');
        $response->assertStatus(404);
    }

    /**
     * 2. Test Admin Gallery CRUD & Hero Toggle
     */
    public function test_admin_can_view_galleries_index_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.galleries.index'));

        $response->assertStatus(200);
        $response->assertSeeText('Pusat Galeri Foto');
        $response->assertSeeText('Tambah Media Baru');
        $response->assertSeeText('★ Hero Profil');
    }

    public function test_admin_can_create_photo_gallery_item(): void
    {
        Storage::fake('public');

        $fakeImage = UploadedFile::fake()->image('tawaf.jpg', 800, 600);

        $payload = [
            'title'           => 'Foto Dokumentasi Thawaf Jamaah',
            'type'            => 'photo',
            'is_profile_hero' => 0,
            'image'           => $fakeImage,
            'caption'         => 'Kekhusyukan jamaah di Masjidil Haram',
            'sort_order'      => 1,
            'is_active'       => 1,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.galleries.store'), $payload);

        $response->assertRedirect(route('admin.galleries.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('galleries', [
            'title'           => 'Foto Dokumentasi Thawaf Jamaah',
            'type'            => 'photo',
            'is_profile_hero' => 0,
        ]);
    }

    public function test_admin_can_create_video_gallery_item_with_youtube_url(): void
    {
        $payload = [
            'title'           => 'Video Profil Baru Zeintour',
            'type'            => 'video',
            'is_profile_hero' => 0,
            'video_url'       => 'https://youtu.be/QrYcpXEC0RU',
            'caption'         => 'Video komitmen pelayanan haji dan umrah',
            'sort_order'      => 1,
            'is_active'       => 1,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.galleries.store'), $payload);

        $response->assertRedirect(route('admin.galleries.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('galleries', [
            'title'     => 'Video Profil Baru Zeintour',
            'type'      => 'video',
            'video_url' => 'https://youtu.be/QrYcpXEC0RU',
        ]);
    }

    /**
     * Test Aturan Penting: Maksimal 1 item aktif per tipe untuk Hero Profil.
     * Mengaktifkan hero baru otomatis menonaktifkan hero lama bertipe sama.
     */
    public function test_single_profile_hero_rule_automatically_deactivates_previous_hero_of_same_type(): void
    {
        // Sebelumnya seeder sudah membuat 1 video dengan is_profile_hero = true
        $previousHeroVideo = Gallery::where('type', 'video')->where('is_profile_hero', true)->first();
        $this->assertNotNull($previousHeroVideo, 'Harus ada hero video awal dari seeder.');

        // Buat video baru dengan is_profile_hero = 1
        $newPayload = [
            'title'           => 'Video Profil Baru Pengganti',
            'type'            => 'video',
            'is_profile_hero' => 1,
            'video_url'       => 'https://youtu.be/dQw4w9WgXcQ',
            'caption'         => 'Video profil terbaru 2026',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.galleries.store'), $newPayload);
        $response->assertRedirect(route('admin.galleries.index'));

        // Cek bahwa video lama otomatis is_profile_hero = false
        $previousHeroVideo->refresh();
        $this->assertFalse((bool) $previousHeroVideo->is_profile_hero, 'Video hero lama harus otomatis dinonaktifkan.');

        // Cek bahwa video baru menjadi is_profile_hero = true
        $newHeroVideo = Gallery::where('title', 'Video Profil Baru Pengganti')->first();
        $this->assertTrue((bool) $newHeroVideo->is_profile_hero, 'Video hero baru harus aktif.');

        // Pastikan jumlah hero video aktif di database tetap tepat 1
        $totalActiveHeroVideos = Gallery::where('type', 'video')->where('is_profile_hero', true)->count();
        $this->assertEquals(1, $totalActiveHeroVideos, 'Jumlah hero video aktif harus tepat 1.');
    }

    public function test_invalid_youtube_url_is_rejected_in_gallery(): void
    {
        $payload = [
            'title'     => 'Video Invalid Link',
            'type'      => 'video',
            'video_url' => 'https://vimeo.com/99999999',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.galleries.store'), $payload);

        $response->assertSessionHasErrors('video_url');
    }

    public function test_admin_can_update_and_delete_gallery_item(): void
    {
        $gallery = Gallery::first();

        $response = $this->actingAs($this->admin)->put(route('admin.galleries.update', $gallery), [
            'title'           => 'Judul Diperbarui Admin',
            'type'            => $gallery->type,
            'is_profile_hero' => $gallery->is_profile_hero ? 1 : 0,
            'video_url'       => $gallery->type === 'video' ? 'https://youtu.be/QrYcpXEC0RU' : null,
            'caption'         => 'Keterangan diperbarui',
            'sort_order'      => 10,
            'is_active'       => 1,
        ]);

        $response->assertRedirect(route('admin.galleries.index'));
        $this->assertDatabaseHas('galleries', [
            'id'    => $gallery->id,
            'title' => 'Judul Diperbarui Admin',
        ]);

        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.galleries.destroy', $gallery));
        $deleteResponse->assertRedirect(route('admin.galleries.index'));
        $this->assertDatabaseMissing('galleries', ['id' => $gallery->id]);
    }

    /**
     * 3. Test Public Pages Rendering (Statis Text + Dinamis Hero Photo & Video)
     */
    public function test_public_profil_page_renders_hero_and_video_from_gallery(): void
    {
        $response = $this->get('/profil');

        $response->assertStatus(200);
        // Assert static content
        $response->assertSee('PT. ZEIN INTERNASIONAL');
        $response->assertSee('IZIN KEMENAG RI NOMOR U.255 TAHUN 2020');
        $response->assertSee('IZIN KEMENAG RI NOMOR 599 TAHUN 2021');
        $response->assertSee('VISI ZEINTOUR');
        $response->assertSee('Misi Utama Pelayanan Ibadah');
        $response->assertSee('Tujuan Berdirinya Zeintour');
        $response->assertSee('Keunggulan Zeintour');

        // Assert dynamic Hero & Video marked with is_profile_hero = true
        $response->assertSee('Gedung Kantor Pusat PT. Zein Internasional');
        $response->assertSee('Profil Resmi PT. Zein Internasional');
        $response->assertSee('https://www.youtube.com/embed/QrYcpXEC0RU');
    }

    public function test_public_galeri_page_renders_active_items_without_category_badges(): void
    {
        $response = $this->get('/galeri');

        $response->assertStatus(200);
        $response->assertSeeText('Momen Ibadah & Kebersamaan Jamaah');
        $response->assertSeeText('Foto Saja');
        $response->assertSeeText('Video Saja');
        $response->assertSeeText('Kekhusyukan Tawaf Jamaah di Depan Ka\'bah');
        $response->assertSee('openVideoModal');
    }

    /**
     * 4. Comparative Render Verification: Desktop Sidebar vs Mobile Bottom-Sheet
     * Memastikan 'Galeri Media' tampil di kedua viewport, dan 'Pengaturan Umum' TIDAK tampil.
     */
    public function test_navigation_renders_galeri_media_and_does_not_render_pengaturan_umum_in_both_viewports(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);

        $html = $response->getContent();

        // 1. Ekstraksi area Desktop Sidebar (<aside class="hidden lg:flex ..."> ... </aside>)
        preg_match('/<aside class="hidden lg:flex[^>]*>(.*?)<\/aside>/s', $html, $sidebarMatches);
        $this->assertNotEmpty($sidebarMatches, 'Desktop Sidebar (<aside>) harus ditemukan di HTML render.');
        $sidebarHtml = $sidebarMatches[1];

        // Assert 'Galeri Media' hadir di Desktop Sidebar
        $this->assertStringContainsString('Galeri Media', $sidebarHtml, 'Menu "Galeri Media" harus ter-render di Desktop Sidebar.');
        $this->assertStringContainsString(route('admin.galleries.index'), $sidebarHtml, 'Link route admin.galleries.index harus ada di Desktop Sidebar.');

        // Assert 'Pengaturan Umum' TIDAK ADA di Desktop Sidebar
        $this->assertStringNotContainsString('Pengaturan Umum', $sidebarHtml, 'Menu "Pengaturan Umum" TIDAK boleh ada di Desktop Sidebar.');
        $this->assertStringNotContainsString('/admin/settings', $sidebarHtml, 'Link /admin/settings TIDAK boleh ada di Desktop Sidebar.');

        // 2. Ekstraksi area Mobile Bottom-Sheet Drawer (<div id="admin-mobile-drawer-menu" ...> ... </div>)
        $drawerStart = strpos($html, 'id="admin-mobile-drawer-menu"');
        $this->assertNotFalse($drawerStart, 'ID "admin-mobile-drawer-menu" harus ditemukan di HTML render.');
        $drawerEnd = strpos($html, '<nav class="fixed bottom-0', $drawerStart);
        $this->assertNotFalse($drawerEnd, 'Tag nav bawah mobile harus ditemukan.');
        $mobileDrawerHtml = substr($html, $drawerStart, $drawerEnd - $drawerStart);

        // Assert 'Galeri Media' hadir di Mobile Bottom-Sheet
        $this->assertStringContainsString('Galeri Media', $mobileDrawerHtml, 'Menu "Galeri Media" harus ter-render di Mobile Bottom-Sheet.');
        $this->assertStringContainsString(route('admin.galleries.index'), $mobileDrawerHtml, 'Link route admin.galleries.index harus ada di Mobile Bottom-Sheet.');

        // Assert 'Pengaturan Umum' TIDAK ADA di Mobile Bottom-Sheet
        $this->assertStringNotContainsString('Pengaturan Umum', $mobileDrawerHtml, 'Menu "Pengaturan Umum" TIDAK boleh ada di Mobile Bottom-Sheet.');
        $this->assertStringNotContainsString('/admin/settings', $mobileDrawerHtml, 'Link /admin/settings TIDAK boleh ada di Mobile Bottom-Sheet.');
    }

    /**
     * 5. Authorization Check
     */
    public function test_non_admin_cannot_access_galleries(): void
    {
        $customer = User::factory()->create([
            'role' => 'jamaah',
        ]);

        $this->actingAs($customer)->get(route('admin.galleries.index'))->assertRedirect();
    }
}


