<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\RegistrationCancellation;
use App\Models\RegistrationMember;
use App\Models\User;
use App\Services\WhatsappOtpInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CancellationValidationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $jamaah1;
    protected User $jamaah2;
    protected Package $package;
    protected Registration $registration1;
    protected Registration $registration2;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock WhatsApp OTP
        $mockWhatsapp = $this->createMock(WhatsappOtpInterface::class);
        $mockWhatsapp->method('sendMessage')->willReturn(true);
        $this->app->instance(WhatsappOtpInterface::class, $mockWhatsapp);

        // 1. Setup Admin
        $this->admin = User::factory()->create([
            'name' => 'Admin Operasional',
            'email' => 'admin@zeintour.com',
            'role' => 'admin',
            'password' => Hash::make('password123'),
            'phone_verified_at' => now(),
        ]);

        // 2. Setup Jamaah 1
        $this->jamaah1 = User::factory()->create([
            'name' => 'Ahmad Farid Kamaludin',
            'email' => 'ahmad.farid@test.com',
            'phone' => '6281234567890',
            'role' => 'jamaah',
            'password' => Hash::make('password123'),
            'phone_verified_at' => now(),
        ]);

        // 3. Setup Jamaah 2
        $this->jamaah2 = User::factory()->create([
            'name' => 'Siti Aisyah',
            'email' => 'siti.aisyah@test.com',
            'phone' => '6289876543210',
            'role' => 'jamaah',
            'password' => Hash::make('password123'),
            'phone_verified_at' => now(),
        ]);

        // 4. Setup Package
        $this->package = Package::create([
            'name' => 'Umrah VIP Eksekutif Awal Musim (12 Hari)',
            'slug' => 'umrah-vip-eksekutif-awal-musim-12-hari',
            'price' => 33900000,
            'duration' => 12,
            'facilities' => 'Hotel *5, Tiket PP Saudi Airlines, Visa Umrah, Bus VIP',
            'departure_date' => now()->addMonths(2),
            'quota' => 30,
            'status' => 'aktif',
        ]);

        // 5. Setup Registration 1 for Jamaah 1 (with upcoming due date)
        $this->registration1 = Registration::create([
            'user_id' => $this->jamaah1->id,
            'package_id' => $this->package->id,
            'status' => Registration::STATUS_MENUNGGU_PEMBAYARAN_DP,
        ]);

        RegistrationMember::create([
            'registration_id' => $this->registration1->id,
            'name' => 'Ahmad Farid Kamaludin',
            'birth_place' => 'Bandung',
            'birth_date' => '1990-05-15',
            'gender' => 'laki-laki',
            'nik' => '3204010101900001',
            'address' => 'Jl. Merdeka No. 10 Bandung',
            'no_kk' => '3204010101900002',
            'relationship' => 'diri_sendiri',
            'document_status' => RegistrationMember::DOC_STATUS_DISETUJUI,
        ]);

        Invoice::create([
            'registration_id' => $this->registration1->id,
            'total_price' => 33900000,
            'total_paid' => 0,
            'remaining_balance' => 33900000,
            'due_date' => now()->addDays(7),
        ]);

        // 6. Setup Registration 2 for Jamaah 2
        $this->registration2 = Registration::create([
            'user_id' => $this->jamaah2->id,
            'package_id' => $this->package->id,
            'status' => Registration::STATUS_MENUNGGU_PEMBAYARAN_DP,
        ]);

        RegistrationMember::create([
            'registration_id' => $this->registration2->id,
            'name' => 'Siti Aisyah',
            'birth_place' => 'Jakarta',
            'birth_date' => '1992-08-20',
            'gender' => 'perempuan',
            'nik' => '3171010101920001',
            'address' => 'Jl. Sudirman No. 5 Jakarta',
            'no_kk' => '3171010101920002',
            'relationship' => 'diri_sendiri',
            'document_status' => RegistrationMember::DOC_STATUS_DISETUJUI,
        ]);

        Invoice::create([
            'registration_id' => $this->registration2->id,
            'total_price' => 33900000,
            'total_paid' => 0,
            'remaining_balance' => 33900000,
            'due_date' => now()->addDays(7),
        ]);
    }

    public function test_guest_and_jamaah_cannot_access_admin_cancellation_routes()
    {
        $cancellation = RegistrationCancellation::create([
            'registration_id' => $this->registration1->id,
            'user_id' => $this->jamaah1->id,
            'reason' => 'Ada perubahan rencana keberangkatan.',
            'status' => RegistrationCancellation::STATUS_PENDING,
        ]);

        // Guest index
        $resGuest = $this->get(route('admin.cancellations.index'));
        $resGuest->assertRedirect(route('admin.login'));

        // Jamaah index
        $resJamaah = $this->actingAs($this->jamaah1)->get(route('admin.cancellations.index'));
        $resJamaah->assertRedirect(route('admin.login'));

        // Jamaah verify
        $resVerify = $this->actingAs($this->jamaah1)->post(route('admin.cancellations.verify', $cancellation), [
            'action' => 'approve',
        ]);
        $resVerify->assertRedirect(route('admin.login'));
    }

    public function test_case_2_jamaah_requests_cancellation_sets_status_pending_and_displays_on_status_page()
    {
        $reason = 'Ada perubahan rencana keberangkatan mendadak.';

        // Jamaah mengajukan pembatalan
        $response = $this->actingAs($this->jamaah1)->post(route('jamaah.registration.cancel.process'), [
            'cancellation_reason' => $reason,
            'agree_terms' => '1',
        ]);

        $response->assertRedirect(route('jamaah.my-registration'));
        $response->assertSessionHas('success');

        // Pastikan record tersimpan di database dengan status pending
        $this->assertDatabaseHas('registration_cancellations', [
            'registration_id' => $this->registration1->id,
            'user_id' => $this->jamaah1->id,
            'reason' => $reason,
            'status' => RegistrationCancellation::STATUS_PENDING,
        ]);

        $this->registration1->refresh();
        $this->assertEquals(RegistrationCancellation::STATUS_PENDING, $this->registration1->cancellation_status);

        // Halaman status jamaah menampilkan banner menunggu validasi admin
        $resStatus = $this->actingAs($this->jamaah1)->get(route('jamaah.my-registration'));
        $resStatus->assertOk();
        $resStatus->assertSee('Pengajuan Pembatalan Sedang Menunggu Validasi Admin');
        $resStatus->assertSee($reason);

        // Halaman form pembatalan menampilkan state sedang diproses
        $resCancel = $this->actingAs($this->jamaah1)->get(route('jamaah.registration.cancel'));
        $resCancel->assertOk();
        $resCancel->assertSee('Pengajuan Pembatalan Sedang Diproses');
        $resCancel->assertSee($reason);
    }

    public function test_admin_can_view_cancellations_with_reasons_clearly_visible()
    {
        $reason = 'Ada perubahan rencana keberangkatan dinas keluarga.';

        RegistrationCancellation::create([
            'registration_id' => $this->registration1->id,
            'user_id' => $this->jamaah1->id,
            'reason' => $reason,
            'status' => RegistrationCancellation::STATUS_PENDING,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.cancellations.index'));
        $response->assertOk();
        $response->assertSee('Ahmad Farid Kamaludin');
        $response->assertSee('Umrah VIP Eksekutif Awal Musim (12 Hari)');
        $response->assertSee($reason);
        $response->assertSee('Menunggu Validasi');
    }

    public function test_case_1_admin_approves_cancellation_and_jamaah_is_strictly_excluded_from_due_date_warning()
    {
        $initialRemaining = $this->package->remaining_quota;

        $cancellation = RegistrationCancellation::create([
            'registration_id' => $this->registration1->id,
            'user_id' => $this->jamaah1->id,
            'reason' => 'Ada perubahan rencana keberangkatan.',
            'status' => RegistrationCancellation::STATUS_PENDING,
        ]);

        $this->registration1->update(['cancellation_status' => RegistrationCancellation::STATUS_PENDING]);

        // Sebelum approve, pastikan Jamaah 1 muncul di Peringatan Batas Waktu Pembayaran di Dashboard
        $resDashboardBefore = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $resDashboardBefore->assertOk();
        $resDashboardBefore->assertSee('Ahmad Farid Kamaludin');

        // Admin klik Setujui Pembatalan
        $response = $this->actingAs($this->admin)->post(route('admin.cancellations.verify', $cancellation), [
            'action' => 'approve',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Pastikan status pembatalan di DB = approved
        $cancellation->refresh();
        $this->assertEquals(RegistrationCancellation::STATUS_APPROVED, $cancellation->status);
        $this->assertEquals($this->admin->id, $cancellation->processed_by);
        $this->assertNotNull($cancellation->processed_at);

        // Pastikan status registrasi = dibatalkan
        $this->registration1->refresh();
        $this->assertEquals(Registration::STATUS_DIBATALKAN, $this->registration1->status);
        $this->assertEquals(RegistrationCancellation::STATUS_APPROVED, $this->registration1->cancellation_status);

        // Pastikan kuota paket bertambah kembali (kursi dilepaskan via remaining_quota)
        $this->package->refresh();
        $this->assertEquals($initialRemaining + 1, $this->package->remaining_quota);

        // ══════════════════════════════════════════════════════════════════════════
        // CRITICAL CHECK: Jamaah 1 TIDAK BOLEH MUNCUL di Peringatan Batas Pembayaran!
        // ══════════════════════════════════════════════════════════════════════════
        $resDashboardAfter = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $resDashboardAfter->assertOk();

        // Cek data view upcomingDueInvoices: hanya Siti Aisyah, TIDAK ADA Ahmad Farid
        $dueInvoices = $resDashboardAfter->viewData('upcomingDueInvoices');
        $dueJamaahNames = $dueInvoices->pluck('registration.user.name')->toArray();
        $this->assertNotContains('Ahmad Farid Kamaludin', $dueJamaahNames);
        $this->assertContains('Siti Aisyah', $dueJamaahNames);

        // Cek totalOutstanding: hanya tersisa tagihan Siti Aisyah (33.900.000)
        $totalOutstanding = $resDashboardAfter->viewData('totalOutstanding');
        $this->assertEquals(33900000.0, $totalOutstanding);

        // Jamaah 1 melihat status pembatalan disetujui di halaman status pendaftarannya
        $resStatus = $this->actingAs($this->jamaah1)->get(route('jamaah.my-registration'));
        $resStatus->assertOk();
        $resStatus->assertSee('Pembatalan Disetujui');

        // Data pendaftaran, invoice, member tetap ada di DB (tidak dihapus)
        $this->assertDatabaseHas('registrations', ['id' => $this->registration1->id]);
        $this->assertDatabaseHas('registration_members', ['registration_id' => $this->registration1->id]);
        $this->assertDatabaseHas('invoices', ['registration_id' => $this->registration1->id]);
    }

    public function test_case_3_admin_cannot_reject_cancellation_without_reason()
    {
        $cancellation = RegistrationCancellation::create([
            'registration_id' => $this->registration1->id,
            'user_id' => $this->jamaah1->id,
            'reason' => 'Ada perubahan rencana keberangkatan.',
            'status' => RegistrationCancellation::STATUS_PENDING,
        ]);

        // Submit kosong
        $resEmpty = $this->actingAs($this->admin)->post(route('admin.cancellations.verify', $cancellation), [
            'action' => 'reject',
            'rejection_reason' => '',
        ]);
        $resEmpty->assertSessionHasErrors(['rejection_reason']);

        // Submit whitespace only
        $resSpace = $this->actingAs($this->admin)->post(route('admin.cancellations.verify', $cancellation), [
            'action' => 'reject',
            'rejection_reason' => '       ',
        ]);
        $resSpace->assertSessionHasErrors(['rejection_reason']);

        $cancellation->refresh();
        $this->assertEquals(RegistrationCancellation::STATUS_PENDING, $cancellation->status);
    }

    public function test_case_3_admin_rejects_cancellation_saves_reason_and_displays_on_jamaah_page()
    {
        $cancellation = RegistrationCancellation::create([
            'registration_id' => $this->registration1->id,
            'user_id' => $this->jamaah1->id,
            'reason' => 'Ada perubahan rencana keberangkatan.',
            'status' => RegistrationCancellation::STATUS_PENDING,
        ]);

        $this->registration1->update(['cancellation_status' => RegistrationCancellation::STATUS_PENDING]);

        $rejectionReason = 'Pembatalan tidak dapat diproses karena tiket penerbangan non-refundable telah diterbitkan.';

        $response = $this->actingAs($this->admin)->post(route('admin.cancellations.verify', $cancellation), [
            'action' => 'reject',
            'rejection_reason' => $rejectionReason,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Pastikan record status rejected dan alasan penolakan tersimpan
        $cancellation->refresh();
        $this->assertEquals(RegistrationCancellation::STATUS_REJECTED, $cancellation->status);
        $this->assertEquals($rejectionReason, $cancellation->rejection_reason);
        $this->assertEquals($this->admin->id, $cancellation->rejected_by);
        $this->assertNotNull($cancellation->rejected_at);

        // Registrasi tetap aktif (status tidak menjadi dibatalkan)
        $this->registration1->refresh();
        $this->assertEquals(Registration::STATUS_MENUNGGU_PEMBAYARAN_DP, $this->registration1->status);
        $this->assertEquals(RegistrationCancellation::STATUS_REJECTED, $this->registration1->cancellation_status);

        // Admin melihat alasan penolakan di index pembatalan
        $resAdmin = $this->actingAs($this->admin)->get(route('admin.cancellations.index'));
        $resAdmin->assertOk();
        $resAdmin->assertSee('Ditolak');
        $resAdmin->assertSee($rejectionReason);

        // Jamaah melihat alasan penolakan di status pendaftaran
        $resJamaah = $this->actingAs($this->jamaah1)->get(route('jamaah.my-registration'));
        $resJamaah->assertOk();
        $resJamaah->assertSee('Pembatalan Ditolak');
        $resJamaah->assertSee($rejectionReason);

        // Jamaah melihat alasan penolakan di form pembatalan
        $resCancelForm = $this->actingAs($this->jamaah1)->get(route('jamaah.registration.cancel'));
        $resCancelForm->assertOk();
        $resCancelForm->assertSee('Pengajuan Pembatalan Sebelumnya Ditolak');
        $resCancelForm->assertSee($rejectionReason);
    }

    public function test_case_4_normal_active_jamaah_behaves_regularly()
    {
        // Jamaah 2 tidak pernah mengajukan pembatalan
        $this->assertNull($this->registration2->cancellation_status);
        $this->assertEmpty($this->registration2->cancellations);

        // Jamaah 2 muncul di Peringatan Batas Waktu Pembayaran di Dashboard
        $resDashboard = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $resDashboard->assertOk();
        $resDashboard->assertSee('Siti Aisyah');

        // Jamaah 2 membuka status pendaftarannya dengan normal
        $resStatus = $this->actingAs($this->jamaah2)->get(route('jamaah.my-registration'));
        $resStatus->assertOk();
        $resStatus->assertDontSee('Pembatalan Ditolak');
        $resStatus->assertDontSee('Pembatalan Disetujui');
        $resStatus->assertDontSee('Pengajuan Pembatalan Sedang Menunggu Validasi Admin');
    }

    public function test_dashboard_statistics_accurately_filters_total_pendaftaran_and_dana_masuk_after_approved_cancellation()
    {
        // 1. Set pembayaran terverifikasi untuk kedua pendaftaran
        // Jamaah 1: Terbayar Rp 20.000.000 (Invoice: total 33.900.000, paid 20.000.000, remaining 13.900.000)
        $invoice1 = $this->registration1->invoice;
        $invoice1->update([
            'total_paid' => 20000000,
            'remaining_balance' => 13900000,
        ]);
        Payment::create([
            'registration_id' => $this->registration1->id,
            'type' => Payment::TYPE_DP,
            'amount' => 20000000,
            'proof_file' => 'payments/proof1.jpg',
            'status' => Payment::STATUS_DISETUJUI,
            'verified_at' => now(),
            'verified_by' => $this->admin->id,
        ]);

        // Jamaah 2: Terbayar Rp 18.900.000 (Invoice: total 33.900.000, paid 18.900.000, remaining 15.000.000)
        $invoice2 = $this->registration2->invoice;
        $invoice2->update([
            'total_paid' => 18900000,
            'remaining_balance' => 15000000,
        ]);
        Payment::create([
            'registration_id' => $this->registration2->id,
            'type' => Payment::TYPE_DP,
            'amount' => 18900000,
            'proof_file' => 'payments/proof2.jpg',
            'status' => Payment::STATUS_DISETUJUI,
            'verified_at' => now(),
            'verified_by' => $this->admin->id,
        ]);

        // ══════════════════════════════════════════════════════════════
        // TAHAP 1: SEBELUM PEMBATALAN (Kedua Jamaah Aktif)
        // ══════════════════════════════════════════════════════════════
        $resBefore = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $resBefore->assertOk();
        $this->assertEquals(2, $resBefore->viewData('totalPendaftaran'));
        $this->assertEquals(38900000.0, $resBefore->viewData('totalIncome')); // 20.000.000 + 18.900.000
        $this->assertEquals(28900000.0, $resBefore->viewData('totalOutstanding')); // 13.900.000 + 15.000.000

        // ══════════════════════════════════════════════════════════════
        // TAHAP 2: STATUS PEMBATALAN = PENDING (Masih Dihitung Aktif)
        // ══════════════════════════════════════════════════════════════
        $cancellation = RegistrationCancellation::create([
            'registration_id' => $this->registration1->id,
            'user_id' => $this->jamaah1->id,
            'reason' => 'Permohonan pembatalan.',
            'status' => RegistrationCancellation::STATUS_PENDING,
        ]);
        $this->registration1->update(['cancellation_status' => RegistrationCancellation::STATUS_PENDING]);

        $resPending = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $resPending->assertOk();
        $this->assertEquals(2, $resPending->viewData('totalPendaftaran'));
        $this->assertEquals(38900000.0, $resPending->viewData('totalIncome'));
        $this->assertEquals(28900000.0, $resPending->viewData('totalOutstanding'));

        // ══════════════════════════════════════════════════════════════
        // TAHAP 3: STATUS PEMBATALAN = REJECTED (Tetap Dihitung Aktif)
        // ══════════════════════════════════════════════════════════════
        $cancellation->update([
            'status' => RegistrationCancellation::STATUS_REJECTED,
            'rejection_reason' => 'Penolakan pembatalan.',
        ]);
        $this->registration1->update(['cancellation_status' => RegistrationCancellation::STATUS_REJECTED]);

        $resRejected = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $resRejected->assertOk();
        $this->assertEquals(2, $resRejected->viewData('totalPendaftaran'));
        $this->assertEquals(38900000.0, $resRejected->viewData('totalIncome'));
        $this->assertEquals(28900000.0, $resRejected->viewData('totalOutstanding'));

        // ══════════════════════════════════════════════════════════════
        // TAHAP 4: STATUS PEMBATALAN = APPROVED (Dikeluarkan dari Aktif)
        // ══════════════════════════════════════════════════════════════
        $this->actingAs($this->admin)->post(route('admin.cancellations.verify', $cancellation), [
            'action' => 'approve',
        ]);

        $resApproved = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $resApproved->assertOk();

        // 1. Total Pendaftaran Aktif = 1 (hanya Jamaah 2)
        $this->assertEquals(1, $resApproved->viewData('totalPendaftaran'));

        // 2. Dana Masuk Aktif = Rp 18.900.000 (hanya pembayaran Jamaah 2, bukan Rp 38.900.000)
        $this->assertEquals(18900000.0, $resApproved->viewData('totalIncome'));

        // 3. Sisa Piutang Aktif = Rp 15.000.000 (hanya sisa tagihan Jamaah 2)
        $this->assertEquals(15000000.0, $resApproved->viewData('totalOutstanding'));

        // 4. Riwayat Transaksi Pembayaran Jamaah 1 Tetap Tersimpan di Database (Audit Trail)
        $this->assertDatabaseHas('payments', [
            'registration_id' => $this->registration1->id,
            'amount' => 20000000,
            'status' => Payment::STATUS_DISETUJUI,
        ]);
        $this->assertDatabaseHas('registrations', [
            'id' => $this->registration1->id,
            'status' => Registration::STATUS_DIBATALKAN,
        ]);
    }

    public function test_approved_cancellation_shows_refund_info_and_whatsapp_button_when_user_has_paid()
    {
        // Set pembayaran terverifikasi untuk Jamaah 1
        $this->registration1->invoice->update([
            'total_paid' => 20000000,
            'remaining_balance' => 13900000,
        ]);
        Payment::create([
            'registration_id' => $this->registration1->id,
            'type' => Payment::TYPE_DP,
            'amount' => 20000000,
            'proof_file' => 'payments/proof_paid.jpg',
            'status' => Payment::STATUS_DISETUJUI,
            'verified_at' => now(),
            'verified_by' => $this->admin->id,
        ]);

        // Pembatalan disetujui
        $cancellation = RegistrationCancellation::create([
            'registration_id' => $this->registration1->id,
            'user_id' => $this->jamaah1->id,
            'reason' => 'Perubahan jadwal dinas.',
            'status' => RegistrationCancellation::STATUS_APPROVED,
            'processed_by' => $this->admin->id,
            'processed_at' => now(),
        ]);
        $this->registration1->update([
            'status' => Registration::STATUS_DIBATALKAN,
            'cancellation_status' => RegistrationCancellation::STATUS_APPROVED,
        ]);

        // Buka halaman status pendaftaran oleh Jamaah 1
        $response = $this->actingAs($this->jamaah1)->get(route('jamaah.my-registration'));
        $response->assertOk();

        // 1. Tampil Pembatalan Disetujui & Keterangan
        $response->assertSee('Pembatalan Disetujui');
        $response->assertSee('Pendaftaran Anda telah dibatalkan.');

        // 2. Tampil Section Pengembalian Dana
        $response->assertSee('Pengembalian Dana');
        $response->assertSee('Karena Anda sudah melakukan pembayaran, untuk proses pengembalian dana silakan hubungi Admin melalui WhatsApp.');
        $response->assertSee('Hubungi Admin via WhatsApp');

        // 3. Tombol WhatsApp berisi link wa.me dan pesan otomatis dengan nama & paket
        $response->assertSee('https://wa.me/', false);
        $response->assertSee(rawurlencode('Halo Admin, saya ingin mengajukan proses pengembalian dana karena pendaftaran saya telah dibatalkan.'), false);
        $response->assertSee(rawurlencode($this->jamaah1->name), false);
        $response->assertSee(rawurlencode($this->package->name), false);

        // 4. Tidak ada reminder pembayaran atau tombol bayar
        $response->assertDontSee('Bayar DP Sekarang');
        $response->assertDontSee('Setor Pelunasan');
        $response->assertDontSee('Batas Waktu DP');
    }

    public function test_approved_cancellation_does_not_show_refund_info_when_user_has_not_paid()
    {
        // Jamaah 2 belum melakukan pembayaran sama sekali (total_paid = 0)
        $this->registration2->invoice->update([
            'total_paid' => 0,
            'remaining_balance' => 33900000,
        ]);

        // Pembatalan disetujui
        $cancellation = RegistrationCancellation::create([
            'registration_id' => $this->registration2->id,
            'user_id' => $this->jamaah2->id,
            'reason' => 'Batal sebelum pembayaran.',
            'status' => RegistrationCancellation::STATUS_APPROVED,
            'processed_by' => $this->admin->id,
            'processed_at' => now(),
        ]);
        $this->registration2->update([
            'status' => Registration::STATUS_DIBATALKAN,
            'cancellation_status' => RegistrationCancellation::STATUS_APPROVED,
        ]);

        // Buka halaman status pendaftaran oleh Jamaah 2
        $response = $this->actingAs($this->jamaah2)->get(route('jamaah.my-registration'));
        $response->assertOk();

        // 1. Tampil Pembatalan Disetujui & Keterangan
        $response->assertSee('Pembatalan Disetujui');
        $response->assertSee('Pendaftaran Anda telah dibatalkan.');

        // 2. TIDAK menampilkan Pengembalian Dana dan TIDAK menampilkan tombol WhatsApp Refund
        $response->assertDontSee('Pengembalian Dana');
        $response->assertDontSee('Karena Anda sudah melakukan pembayaran');
        $response->assertDontSee('Hubungi Admin via WhatsApp');

        // 3. Tidak ada reminder pembayaran atau tombol bayar
        $response->assertDontSee('Bayar DP Sekarang');
        $response->assertDontSee('Setor Pelunasan');
        $response->assertDontSee('Batas Waktu DP');
    }
}
