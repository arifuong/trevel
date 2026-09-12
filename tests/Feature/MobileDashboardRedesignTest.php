<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Package;
use App\Models\Registration;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MobileDashboardRedesignTest extends TestCase
{
    use RefreshDatabase;

    public function test_jamaah_dashboard_renders_mobile_and_desktop_sections(): void
    {
        $jamaah = User::factory()->create([
            'role' => 'jamaah',
            'name' => 'Fulan bin Fulan',
            'phone' => '081234567890',
        ]);

        $response = $this->actingAs($jamaah)->get(route('jamaah.dashboard'));

        $response->assertStatus(200);
        // Mobile header & role
        $response->assertSee('PT. Zein Internasional');
        $response->assertSee('Fulan');
        // Mobile & Desktop elements
        $response->assertSee('lg:hidden', false);
        $response->assertSee('hidden lg:block', false);
        $response->assertSee('Menu Layanan');
        $response->assertSee('Panduan Ibadah');
    }

    public function test_jamaah_dashboard_with_active_registration_renders_correctly(): void
    {
        $jamaah = User::factory()->create([
            'role' => 'jamaah',
            'name' => 'Ahmad Shodiq',
            'phone' => '081234567891',
        ]);

        $package = Package::create([
            'name' => 'Umrah Syawal Berkah',
            'slug' => 'umrah-syawal-berkah',
            'price' => 35000000,
            'duration' => 12,
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'facilities' => 'Hotel Bintang 5, Visa Umrah, Tiket PP',
            'quota' => 40,
            'status' => 'aktif',
        ]);

        $registration = Registration::create([
            'user_id' => $jamaah->id,
            'package_id' => $package->id,
            'status' => 'menunggu_pembayaran_dp',
            'step_number' => 2,
        ]);

        Invoice::create([
            'registration_id' => $registration->id,
            'total_price' => 35000000,
            'total_paid' => 0,
            'remaining_balance' => 35000000,
            'due_date' => now()->addDays(14),
        ]);

        $response = $this->actingAs($jamaah)->get(route('jamaah.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Umrah Syawal Berkah');
        $response->assertSee('Bayar DP');
        $response->assertSee('Tahap 2 dari 9');
        $response->assertSee('22%');
        $response->assertDontSee('Tahap 2/7');
        $response->assertDontSee('dari 7');
        $response->assertDontSee('/ 7');
        $response->assertSee('Dokumen');
        $response->assertSee('Pembayaran');
        $response->assertSee('Paket &amp; Manasik', false);
        $response->assertSee('Bantuan CS');
    }

    public function test_completed_registration_shows_100_percent_and_no_invalid_fractions(): void
    {
        $jamaah = User::factory()->create([
            'role' => 'jamaah',
            'name' => 'Hajjah Siti',
            'phone' => '081234567892',
        ]);

        $package = Package::create([
            'name' => 'Umrah Ramadhan Akbar',
            'slug' => 'umrah-ramadhan-akbar',
            'price' => 45000000,
            'duration' => 15,
            'departure_date' => now()->subDays(20)->format('Y-m-d'),
            'facilities' => 'Full Board Bintang 5',
            'quota' => 45,
            'status' => 'aktif',
        ]);

        $registration = Registration::create([
            'user_id' => $jamaah->id,
            'package_id' => $package->id,
            'status' => Registration::STATUS_SELESAI,
        ]);

        Invoice::create([
            'registration_id' => $registration->id,
            'total_price' => 45000000,
            'total_paid' => 45000000,
            'remaining_balance' => 0,
            'due_date' => now()->subDays(30),
        ]);

        $this->assertEquals(9, $registration->step_number);
        $this->assertEquals(9, $registration->total_steps);
        $this->assertEquals(100, $registration->progress_percentage);

        $response = $this->actingAs($jamaah)->get(route('jamaah.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('100%');
        $response->assertSee('Perjalanan Tuntas');
        $response->assertDontSee('129%');
        $response->assertDontSee('Tahap 9/7');
        $response->assertDontSee('dari 7');
        $response->assertDontSee('/ 7');
    }

    public function test_registration_progress_percentage_calculation_for_all_statuses(): void
    {
        $statuses = [
            Registration::STATUS_MENUNGGU_VERIFIKASI_DOKUMEN => [1, 11],
            Registration::STATUS_MENUNGGU_PEMBAYARAN_DP => [2, 22],
            Registration::STATUS_MENUNGGU_VERIFIKASI_PEMBAYARAN_DP => [3, 33],
            Registration::STATUS_JAMAAH => [4, 44],
            Registration::STATUS_CICILAN_PELUNASAN => [5, 56],
            Registration::STATUS_LUNAS => [6, 67],
            Registration::STATUS_MENUNGGU_KELENGKAPAN_KEBERANGKATAN => [7, 78],
            Registration::STATUS_BERANGKAT => [8, 89],
            Registration::STATUS_SELESAI => [9, 100],
            Registration::STATUS_DIBATALKAN => [0, 0],
        ];

        foreach ($statuses as $status => [$expectedStep, $expectedPct]) {
            $reg = new Registration(['status' => $status]);
            $this->assertEquals($expectedStep, $reg->step_number, "Step for status {$status} mismatch");
            $this->assertEquals($expectedPct, $reg->progress_percentage, "Percentage for status {$status} mismatch");
            $this->assertLessThanOrEqual(100, $reg->progress_percentage);
            $this->assertGreaterThanOrEqual(0, $reg->progress_percentage);
        }
    }

    public function test_admin_dashboard_renders_mobile_and_desktop_sections(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'name' => 'Admin Utama',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        // Mobile header
        $response->assertSee('Pusat Kendali Operasional');
        $response->assertSee('Halo, Admin');
        // Mobile card utama
        $response->assertSee('Ringkasan Sistem');
        $response->assertSee('Dana Masuk');
        $response->assertSee('Sisa Tagihan');
        // Mobile quick menus
        $response->assertSee('Menu Cepat Admin');
        $response->assertSee('Perlu Tindakan');
        // Mobile & desktop separation
        $response->assertSee('lg:hidden', false);
        $response->assertSee('hidden lg:block', false);
    }
}

