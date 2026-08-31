<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\RegistrationMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $jamaah1;
    protected User $jamaah2;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        // 1. Akun Admin
        $this->admin = User::factory()->create([
            'name' => 'Administrator Zein',
            'email' => 'admin@zeintour.com',
            'phone' => '6282121483337',
            'role' => 'admin',
            'password' => Hash::make('admin123'),
            'phone_verified_at' => now(),
        ]);

        // 2. Akun Jamaah 1 (Terverifikasi & Punya Pendaftaran)
        $this->jamaah1 = User::factory()->create([
            'name' => 'Arif Hasbi',
            'email' => 'arif@zeintour.test',
            'phone' => '6281234567890',
            'role' => 'jamaah',
            'password' => Hash::make('password123'),
            'phone_verified_at' => now(),
            'gender' => 'laki-laki',
            'birth_place' => 'Bandung',
            'birth_date' => '1990-01-01',
            'address' => 'Jl. Terusan Buah Batu No. 12',
        ]);

        // 3. Akun Jamaah 2 (Belum Verifikasi & Belum Daftar)
        $this->jamaah2 = User::factory()->create([
            'name' => 'Ahmad Fauzan',
            'email' => 'ahmad.fauzan@zeintour.test',
            'phone' => '6281399887766',
            'role' => 'jamaah',
            'password' => Hash::make('password123'),
            'phone_verified_at' => null,
        ]);
    }

    protected function createPackage(string $name = 'Paket Umrah Syawal 1447H'): Package
    {
        return Package::create([
            'name' => $name,
            'slug' => str($name)->slug(),
            'price' => 30000000,
            'departure_date' => now()->addDays(45),
            'duration' => 9,
            'quota' => 45,
            'facilities' => "Hotel Makkah\nHotel Madinah\nTiket PP",
            'status' => 'aktif',
        ]);
    }

    /**
     * 1. Admin dapat membuka halaman Manajemen Jamaah.
     */
    public function test_admin_can_view_user_management_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.index'));

        $response->assertOk();
        $response->assertSee('Manajemen Jamaah');
        $response->assertSee('Kelola dan lihat seluruh jamaah yang telah terdaftar dalam sistem.');
        $response->assertSee('Arif Hasbi');
        $response->assertSee('Ahmad Fauzan');
        $response->assertSee('Lihat Detail');
    }

    /**
     * 2. Tamu (Guest) dan Jamaah biasa tidak boleh mengakses Manajemen Jamaah Admin.
     */
    public function test_guest_and_regular_jamaah_cannot_access_admin_user_management(): void
    {
        // Guest
        $guestResponse = $this->get(route('admin.users.index'));
        $guestResponse->assertRedirect(route('admin.login'));

        // Jamaah biasa
        $jamaahResponse = $this->actingAs($this->jamaah1)->get(route('admin.users.index'));
        $jamaahResponse->assertRedirect(route('admin.login'));
    }

    /**
     * 3. Pencarian server-side berdasarkan Nama, Email, dan Nomor WhatsApp.
     */
    public function test_admin_can_search_users_by_name_email_and_phone(): void
    {
        // Cari Nama "Arif"
        $resName = $this->actingAs($this->admin)->get(route('admin.users.index', ['search' => 'Arif']));
        $resName->assertSee('Arif Hasbi');
        $resName->assertDontSee('Ahmad Fauzan');

        // Cari Email "ahmad.fauzan"
        $resEmail = $this->actingAs($this->admin)->get(route('admin.users.index', ['search' => 'ahmad.fauzan@zeintour.test']));
        $resEmail->assertSee('Ahmad Fauzan');
        $resEmail->assertDontSee('Arif Hasbi');

        // Cari WhatsApp "62812345"
        $resPhone = $this->actingAs($this->admin)->get(route('admin.users.index', ['search' => '62812345']));
        $resPhone->assertSee('Arif Hasbi');
        $resPhone->assertDontSee('Ahmad Fauzan');
    }

    /**
     * 4. Pencarian server-side berdasarkan NIK dan Nomor Paspor anggota jamaah.
     */
    public function test_admin_can_search_users_by_nik_and_passport(): void
    {
        $package = $this->createPackage('Umrah Syawal Berkah 1447H');

        $registration = Registration::create([
            'user_id' => $this->jamaah1->id,
            'package_id' => $package->id,
            'status' => Registration::STATUS_JAMAAH,
            'created_at' => now(),
        ]);

        RegistrationMember::create([
            'registration_id' => $registration->id,
            'name' => 'Arif Hasbi',
            'nik' => '3273010101900001',
            'no_kk' => '3273010101909999',
            'no_passport' => 'B1234567',
            'relationship' => 'diri_sendiri',
            'document_status' => RegistrationMember::DOC_STATUS_DISETUJUI,
        ]);

        // Cari berdasarkan NIK
        $resNik = $this->actingAs($this->admin)->get(route('admin.users.index', ['search' => '3273010101900001']));
        $resNik->assertSee('Arif Hasbi');
        $resNik->assertDontSee('Ahmad Fauzan');

        // Cari berdasarkan Paspor
        $resPass = $this->actingAs($this->admin)->get(route('admin.users.index', ['search' => 'B1234567']));
        $resPass->assertSee('Arif Hasbi');
        $resPass->assertDontSee('Ahmad Fauzan');
    }

    /**
     * 5. Filter status pendaftaran dan pembatalan.
     */
    public function test_admin_can_filter_users_by_status(): void
    {
        $package = $this->createPackage('Umrah Syawal Berkah 1447H');

        Registration::create([
            'user_id' => $this->jamaah1->id,
            'package_id' => $package->id,
            'status' => Registration::STATUS_JAMAAH,
        ]);

        // Filter Terverifikasi
        $resVerified = $this->actingAs($this->admin)->get(route('admin.users.index', ['status' => 'terverifikasi']));
        $resVerified->assertSee('Arif Hasbi');
        $resVerified->assertDontSee('Ahmad Fauzan');

        // Filter Belum Daftar Paket
        $resNoReg = $this->actingAs($this->admin)->get(route('admin.users.index', ['status' => 'belum_daftar']));
        $resNoReg->assertSee('Ahmad Fauzan');
        $resNoReg->assertDontSee('Arif Hasbi');
    }

    /**
     * 5b. Jamaah yang dibatalkan tetap muncul di Manajemen Jamaah dengan status Dibatalkan.
     */
    public function test_cancelled_jamaah_remains_visible_in_manajemen_jamaah(): void
    {
        $package = $this->createPackage('Paket Batal Test');

        $regCancelled = Registration::create([
            'user_id' => $this->jamaah1->id,
            'package_id' => $package->id,
            'status' => Registration::STATUS_DIBATALKAN,
            'cancellation_status' => 'approved',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.users.index'));
        $response->assertOk();
        $response->assertSee('Arif Hasbi');
        $response->assertSee('Dibatalkan');

        // Filter khusus Dibatalkan
        $filterRes = $this->actingAs($this->admin)->get(route('admin.users.index', ['status' => 'dibatalkan']));
        $filterRes->assertOk();
        $filterRes->assertSee('Arif Hasbi');
        $filterRes->assertDontSee('Ahmad Fauzan');
    }

    /**
     * 6. Admin dapat membuka detail lengkap jamaah (Profil, Dokumen, Pendaftaran, Pembayaran).
     */
    public function test_admin_can_view_user_detail_page(): void
    {
        $package = $this->createPackage('Umrah Reguler Ramadhan 1447H');

        $registration = Registration::create([
            'user_id' => $this->jamaah1->id,
            'package_id' => $package->id,
            'status' => Registration::STATUS_JAMAAH,
        ]);

        $ktpPath = 'documents/ktp_test.jpg';
        Storage::disk('public')->put($ktpPath, 'fake-ktp-content');

        $member = RegistrationMember::create([
            'registration_id' => $registration->id,
            'name' => 'Arif Hasbi',
            'nik' => '3273010101900001',
            'no_kk' => '3273010101909999',
            'no_passport' => 'B9876543',
            'relationship' => 'diri_sendiri',
            'ktp_file' => $ktpPath,
            'document_status' => RegistrationMember::DOC_STATUS_DISETUJUI,
        ]);

        $invoice = Invoice::create([
            'registration_id' => $registration->id,
            'total_price' => 30000000,
            'dp_amount' => 5000000,
            'total_paid' => 5000000,
            'remaining_balance' => 25000000,
            'due_date' => now()->addDays(30),
            'status' => 'belum_lunas',
        ]);

        Payment::create([
            'registration_id' => $registration->id,
            'type' => 'dp',
            'amount' => 5000000,
            'proof_file' => 'payments/proof_dp.jpg',
            'status' => Payment::STATUS_DISETUJUI,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.users.show', $this->jamaah1->id));

        $response->assertOk();
        $response->assertSee('Arif Hasbi');
        $response->assertSee('arif@zeintour.test');
        $response->assertSee('+62 812-3456-7890');
        $response->assertSee('3273010101900001');
        $response->assertSee('B9876543');
        $response->assertSee('Umrah Reguler Ramadhan 1447H');
        $response->assertSee('Rp 30.000.000');
        $response->assertSee('Rp 5.000.000');
        $response->assertSee('Rp 25.000.000');
        $response->assertSee('Dokumen Persyaratan Ibadah');
    }

    /**
     * 7. Admin dapat melihat preview dokumen secara aman dan non-admin ditolak.
     */
    public function test_admin_can_securely_preview_documents(): void
    {
        $package = $this->createPackage('Paket Preview Test');
        $registration = Registration::create([
            'user_id' => $this->jamaah1->id,
            'package_id' => $package->id,
            'status' => Registration::STATUS_MENUNGGU_VERIFIKASI_DOKUMEN,
        ]);

        $ktpPath = 'documents/ktp_secure.jpg';
        Storage::disk('public')->put($ktpPath, 'binary-image-data');

        $member = RegistrationMember::create([
            'registration_id' => $registration->id,
            'name' => 'Arif Hasbi',
            'nik' => '3273010101900001',
            'no_kk' => '3273010101909999',
            'ktp_file' => $ktpPath,
            'relationship' => 'diri_sendiri',
            'document_status' => RegistrationMember::DOC_STATUS_MENUNGGU_VERIFIKASI,
        ]);

        // Admin berhasil preview dokumen
        $adminRes = $this->actingAs($this->admin)->get(route('admin.members.documents.preview', [
            'member' => $member->id,
            'type' => 'ktp_file',
        ]));
        $adminRes->assertOk();

        // Jamaah biasa ditolak mengakses preview dokumen admin
        $jamaahRes = $this->actingAs($this->jamaah1)->get(route('admin.members.documents.preview', [
            'member' => $member->id,
            'type' => 'ktp_file',
        ]));
        $jamaahRes->assertRedirect(route('admin.login'));
    }
}
