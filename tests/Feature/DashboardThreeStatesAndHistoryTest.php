<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Package;
use App\Models\Registration;
use App\Models\RegistrationMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardThreeStatesAndHistoryTest extends TestCase
{
    use RefreshDatabase;

    protected function createPackage(string $name = 'Paket Umrah Reguler 12 Hari'): Package
    {
        return Package::create([
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name) . '-' . uniqid(),
            'price' => 30000000,
            'duration' => 12,
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'facilities' => 'Hotel Bintang 5',
            'quota' => 30,
            'status' => 'aktif',
        ]);
    }

    /**
     * Test Kondisi A: User has active booking (status != selesai)
     */
    public function test_state_a_renders_active_booking_cards(): void
    {
        $user = User::factory()->create(['role' => 'jamaah', 'name' => 'Ahmad Active']);
        $package = $this->createPackage('Umrah Ramadhan 1448H');

        $reg = Registration::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'status' => Registration::STATUS_MENUNGGU_PEMBAYARAN_DP,
            'step_number' => 2,
        ]);

        Invoice::create([
            'registration_id' => $reg->id,
            'total_price' => 30000000,
            'total_paid' => 0,
            'remaining_balance' => 30000000,
            'due_date' => now()->addDays(14),
        ]);

        $response = $this->actingAs($user)->get(route('jamaah.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Umrah Ramadhan 1448H');
        $response->assertSee('Tahap 2 dari 9');
        $response->assertSee('22%');
        $response->assertSee('Bayar DP');
        $response->assertDontSee('Pilih Paket Umrah/Haji Pertama Anda');
    }

    /**
     * Test Kondisi A with multiple active bookings: All active bookings rendered
     */
    public function test_state_a_with_multiple_active_bookings_renders_all_cards(): void
    {
        $user = User::factory()->create(['role' => 'jamaah', 'name' => 'Budi Multi']);
        $pkg1 = $this->createPackage('Umrah Syawal Family');
        $pkg2 = $this->createPackage('Haji Plus Furoda');

        $reg1 = Registration::create([
            'user_id' => $user->id,
            'package_id' => $pkg1->id,
            'status' => Registration::STATUS_MENUNGGU_VERIFIKASI_DOKUMEN,
            'step_number' => 1,
        ]);

        $reg2 = Registration::create([
            'user_id' => $user->id,
            'package_id' => $pkg2->id,
            'status' => Registration::STATUS_JAMAAH,
            'step_number' => 4,
        ]);

        $response = $this->actingAs($user)->get(route('jamaah.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Umrah Syawal Family');
        $response->assertSee('Haji Plus Furoda');
        $response->assertSee('Pendaftaran Aktif');
    }

    /**
     * Test Kondisi B: All bookings are completed ('selesai')
     */
    public function test_state_b_renders_warm_thank_you_and_ctas(): void
    {
        $user = User::factory()->create(['role' => 'jamaah', 'name' => 'Siti Completed']);
        $package = $this->createPackage('Umrah Milad Berkah');

        $reg = Registration::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'status' => Registration::STATUS_SELESAI,
            'step_number' => 9,
        ]);

        Invoice::create([
            'registration_id' => $reg->id,
            'total_price' => 32000000,
            'total_paid' => 32000000,
            'remaining_balance' => 0,
            'due_date' => now()->subMonth(),
        ]);

        $response = $this->actingAs($user)->get(route('jamaah.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Terima kasih telah menyelesaikan ibadah Umrah bersama PT Zein Internasional 🕋');
        $response->assertSee('Pilih Paket Umrah/Haji Berikutnya');
        $response->assertSee('Lihat Riwayat Perjalanan Saya');
        $response->assertSee('Perjalanan Tuntas');
        $response->assertSee('100%');
        $response->assertSee('Umrah Milad Berkah');
    }

    /**
     * Test Kondisi C: Brand new user with no bookings at all
     */
    public function test_state_c_renders_welcoming_onboarding_and_catalog_cta(): void
    {
        $user = User::factory()->create(['role' => 'jamaah', 'name' => 'Calon Jamaah Baru']);

        $response = $this->actingAs($user)->get(route('jamaah.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Wujudkan Niat Suci Menuju Baitullah');
        $response->assertSee('Pilih Paket Umrah/Haji Pertama Anda');
        $response->assertSee('Daftar Paket Sekarang');
        $response->assertDontSee('Lihat Riwayat Perjalanan Saya');
    }

    /**
     * Test Jamaah Riwayat Perjalanan Page
     */
    public function test_jamaah_history_page_renders_completed_trips_and_links(): void
    {
        $user = User::factory()->create(['role' => 'jamaah', 'name' => 'Riwayat Jamaah']);
        $package = $this->createPackage('Umrah Awal Tahun');

        $reg = Registration::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'status' => Registration::STATUS_SELESAI,
            'step_number' => 9,
        ]);

        RegistrationMember::create([
            'registration_id' => $reg->id,
            'name' => 'Riwayat Jamaah',
            'nik' => '3201010101010001',
            'no_kk' => '3201010101010001',
            'relationship' => 'self',
        ]);

        Invoice::create([
            'registration_id' => $reg->id,
            'total_price' => 28000000,
            'total_paid' => 28000000,
            'remaining_balance' => 0,
            'due_date' => now()->subMonth(),
        ]);

        $response = $this->actingAs($user)->get(route('jamaah.history'));

        $response->assertStatus(200);
        $response->assertSee('Riwayat Perjalanan Ibadah');
        $response->assertSee('Umrah Awal Tahun');
        $response->assertSee($reg->registration_number);
        $response->assertSee('Selesai');

        // Test alias redirect /portal/riwayat
        $redirectResponse = $this->actingAs($user)->get('/portal/riwayat');
        $redirectResponse->assertRedirect(route('jamaah.history'));
    }

    /**
     * Test Admin Registrations Tab Separation (Aktif vs Riwayat)
     */
    public function test_admin_registrations_tab_separation(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $jamaah = User::factory()->create(['role' => 'jamaah']);

        $pkgActive = $this->createPackage('Paket Booking Aktif');
        $pkgCompleted = $this->createPackage('Paket Booking Selesai');

        $regActive = Registration::create([
            'user_id' => $jamaah->id,
            'package_id' => $pkgActive->id,
            'status' => Registration::STATUS_MENUNGGU_VERIFIKASI_DOKUMEN,
        ]);

        $regCompleted = Registration::create([
            'user_id' => $jamaah->id,
            'package_id' => $pkgCompleted->id,
            'status' => Registration::STATUS_SELESAI,
        ]);

        // Default tab (aktif)
        $responseActive = $this->actingAs($admin)->get(route('admin.registrations.index'));
        $responseActive->assertStatus(200);
        $responseActive->assertSee('Booking Aktif');
        $responseActive->assertSee('Riwayat Selesai');
        $responseActive->assertSee($regActive->registration_number);
        $responseActive->assertDontSee($regCompleted->registration_number);

        // Riwayat tab
        $responseHistory = $this->actingAs($admin)->get(route('admin.registrations.index', ['tab' => 'riwayat']));
        $responseHistory->assertStatus(200);
        $responseHistory->assertSee($regCompleted->registration_number);
        $responseHistory->assertDontSee($regActive->registration_number);
        $responseHistory->assertSee('Total Perjalanan Selesai');
        $responseHistory->assertSee('Total Nilai Transaksi Tuntas');
    }
}
