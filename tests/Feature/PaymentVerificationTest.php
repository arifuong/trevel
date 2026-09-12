<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\RegistrationMember;
use App\Models\User;
use App\Contracts\WhatsAppNotificationInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentVerificationTest extends TestCase
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

        Storage::fake('public');

        // Mock WhatsApp Notification
        $mockWhatsapp = $this->createMock(WhatsAppNotificationInterface::class);
        $mockWhatsapp->method('sendMessage')->willReturn(true);
        $this->app->instance(WhatsAppNotificationInterface::class, $mockWhatsapp);

        // 1. Admin
        $this->admin = User::factory()->create([
            'name' => 'Admin Keuangan',
            'email' => 'finance@zeintour.com',
            'role' => 'admin',
            'password' => Hash::make('password123'),
        ]);

        // 2. Jamaah 1
        $this->jamaah1 = User::factory()->create([
            'name' => 'Ahmad Jamaah',
            'email' => 'ahmad@test.com',
            'phone' => '6281111111111',
            'role' => 'jamaah',
            'password' => Hash::make('password123'),
        ]);

        // 3. Jamaah 2
        $this->jamaah2 = User::factory()->create([
            'name' => 'Budi Jamaah',
            'email' => 'budi@test.com',
            'phone' => '6282222222222',
            'role' => 'jamaah',
            'password' => Hash::make('password123'),
        ]);

        // 4. Package
        $this->package = Package::create([
            'name' => 'Paket Umrah Reguler 12 Hari',
            'slug' => 'paket-umrah-reguler-12-hari',
            'price' => 30000000,
            'duration' => 12,
            'facilities' => 'Hotel *4, Tiket PP, Visa, Bus AC, Makan 3x',
            'departure_date' => now()->addMonths(2),
            'quota' => 45,
            'status' => 'aktif',
        ]);

        // 5. Registration 1 for Jamaah 1
        $this->registration1 = Registration::create([
            'user_id' => $this->jamaah1->id,
            'package_id' => $this->package->id,
            'status' => Registration::STATUS_MENUNGGU_VERIFIKASI_PEMBAYARAN_DP,
        ]);

        RegistrationMember::create([
            'registration_id' => $this->registration1->id,
            'name' => 'Ahmad Jamaah',
            'nik' => '3201010101010001',
            'no_kk' => '3201010101010002',
            'ktp_file' => 'documents/ktp/ktp1.jpg',
            'kk_file' => 'documents/kk/kk1.jpg',
            'relationship' => 'diri_sendiri',
            'document_status' => RegistrationMember::DOC_STATUS_DISETUJUI,
        ]);

        Invoice::create([
            'registration_id' => $this->registration1->id,
            'total_price' => 30000000,
            'total_paid' => 0,
            'remaining_balance' => 30000000,
            'due_date' => now()->addDays(7),
        ]);

        // 6. Registration 2 for Jamaah 2
        $this->registration2 = Registration::create([
            'user_id' => $this->jamaah2->id,
            'package_id' => $this->package->id,
            'status' => Registration::STATUS_MENUNGGU_PEMBAYARAN_DP,
        ]);

        RegistrationMember::create([
            'registration_id' => $this->registration2->id,
            'name' => 'Budi Jamaah',
            'nik' => '3202020202020001',
            'no_kk' => '3202020202020002',
            'ktp_file' => 'documents/ktp/ktp2.jpg',
            'kk_file' => 'documents/kk/kk2.jpg',
            'relationship' => 'diri_sendiri',
            'document_status' => RegistrationMember::DOC_STATUS_DISETUJUI,
        ]);

        Invoice::create([
            'registration_id' => $this->registration2->id,
            'total_price' => 30000000,
            'total_paid' => 0,
            'remaining_balance' => 30000000,
            'due_date' => now()->addDays(7),
        ]);
    }

    public function test_guest_and_regular_jamaah_cannot_verify_payment()
    {
        $payment = Payment::create([
            'registration_id' => $this->registration1->id,
            'type' => Payment::TYPE_DP,
            'amount' => 5000000,
            'proof_file' => 'payments/dp/proof1.jpg',
            'status' => Payment::STATUS_MENUNGGU_VERIFIKASI,
        ]);

        // Guest
        $resGuest = $this->post(route('admin.payments.verify', $payment), [
            'action' => 'approve',
        ]);
        $resGuest->assertRedirect(route('admin.login'));

        // Regular Jamaah Web Request
        $resJamaah = $this->actingAs($this->jamaah1)->post(route('admin.payments.verify', $payment), [
            'action' => 'approve',
        ]);
        $resJamaah->assertRedirect(route('admin.login'));

        // Regular Jamaah JSON Request
        $resJamaahJson = $this->actingAs($this->jamaah1)->postJson(route('admin.payments.verify', $payment), [
            'action' => 'approve',
        ]);
        $resJamaahJson->assertForbidden();
    }

    public function test_admin_cannot_reject_payment_without_rejection_reason()
    {
        $payment = Payment::create([
            'registration_id' => $this->registration1->id,
            'type' => Payment::TYPE_DP,
            'amount' => 5000000,
            'proof_file' => 'payments/dp/proof1.jpg',
            'status' => Payment::STATUS_MENUNGGU_VERIFIKASI,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.payments.verify', $payment), [
            'action' => 'reject',
            'rejection_reason' => '',
        ]);

        $response->assertSessionHasErrors(['rejection_reason']);
        $payment->refresh();
        $this->assertEquals(Payment::STATUS_MENUNGGU_VERIFIKASI, $payment->status);
    }

    public function test_admin_cannot_reject_payment_with_whitespace_only_reason()
    {
        $payment = Payment::create([
            'registration_id' => $this->registration1->id,
            'type' => Payment::TYPE_DP,
            'amount' => 5000000,
            'proof_file' => 'payments/dp/proof1.jpg',
            'status' => Payment::STATUS_MENUNGGU_VERIFIKASI,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.payments.verify', $payment), [
            'action' => 'reject',
            'rejection_reason' => '     ',
        ]);

        $response->assertSessionHasErrors(['rejection_reason']);
        $payment->refresh();
        $this->assertEquals(Payment::STATUS_MENUNGGU_VERIFIKASI, $payment->status);
    }

    public function test_admin_rejects_dp_payment_saves_reason_rejected_by_and_updates_registration_status()
    {
        $payment = Payment::create([
            'registration_id' => $this->registration1->id,
            'type' => Payment::TYPE_DP,
            'amount' => 5000000,
            'proof_file' => 'payments/dp/proof1.jpg',
            'status' => Payment::STATUS_MENUNGGU_VERIFIKASI,
        ]);

        $reason = 'Nominal pembayaran tidak sesuai dengan jumlah tagihan DP.';

        $response = $this->actingAs($this->admin)->post(route('admin.payments.verify', $payment), [
            'action' => 'reject',
            'rejection_reason' => $reason,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $payment->refresh();
        $this->assertEquals(Payment::STATUS_DITOLAK, $payment->status);
        $this->assertEquals($reason, $payment->rejection_reason);
        $this->assertEquals($this->admin->id, $payment->rejected_by);
        $this->assertNotNull($payment->rejected_at);
        $this->assertNull($payment->verified_by);
        $this->assertNull($payment->verified_at);

        $this->registration1->refresh();
        $this->assertEquals(Registration::STATUS_MENUNGGU_PEMBAYARAN_DP, $this->registration1->status);
    }

    public function test_jamaah_sees_rejection_reason_on_status_page_and_dp_form()
    {
        $reason = 'Foto bukti transfer terpotong dan tidak menampilkan nomor referensi.';

        Payment::create([
            'registration_id' => $this->registration1->id,
            'type' => Payment::TYPE_DP,
            'amount' => 5000000,
            'proof_file' => 'payments/dp/proof_rejected.jpg',
            'status' => Payment::STATUS_DITOLAK,
            'rejection_reason' => $reason,
            'rejected_by' => $this->admin->id,
            'rejected_at' => now(),
        ]);

        $this->registration1->update([
            'status' => Registration::STATUS_MENUNGGU_PEMBAYARAN_DP,
        ]);

        // Cek halaman status pendaftaran
        $resStatus = $this->actingAs($this->jamaah1)->get(route('jamaah.my-registration'));
        $resStatus->assertOk();
        $resStatus->assertSee('Pembayaran DP Ditolak');
        $resStatus->assertSee($reason);
        $resStatus->assertSee('Upload Bukti Pembayaran DP');

        // Cek halaman formulir upload DP
        $resDp = $this->actingAs($this->jamaah1)->get(route('jamaah.payment.dp'));
        $resDp->assertOk();
        $resDp->assertSee('Pembayaran DP Sebelumnya Ditolak');
        $resDp->assertSee($reason);
    }

    public function test_jamaah_reupload_dp_proof_creates_new_payment_with_pending_status_and_preserves_history()
    {
        $oldPayment = Payment::create([
            'registration_id' => $this->registration1->id,
            'type' => Payment::TYPE_DP,
            'amount' => 5000000,
            'proof_file' => 'payments/dp/old_rejected.jpg',
            'status' => Payment::STATUS_DITOLAK,
            'rejection_reason' => 'Struk buram.',
            'rejected_by' => $this->admin->id,
            'rejected_at' => now()->subDay(),
        ]);

        $this->registration1->update([
            'status' => Registration::STATUS_MENUNGGU_PEMBAYARAN_DP,
        ]);

        // Jamaah upload bukti DP baru
        $newFile = UploadedFile::fake()->image('bukti_dp_baru.jpg', 600, 800);
        $resUpload = $this->actingAs($this->jamaah1)->post(route('jamaah.payment.dp.store'), [
            'amount' => 5000000,
            'proof_file' => $newFile,
        ]);

        $resUpload->assertRedirect(route('jamaah.my-registration'));
        $resUpload->assertSessionHas('success');

        // Pastikan status registrasi kembali ke menunggu verifikasi DP
        $this->registration1->refresh();
        $this->assertEquals(Registration::STATUS_MENUNGGU_VERIFIKASI_PEMBAYARAN_DP, $this->registration1->status);

        // Pastikan ada 2 pembayaran (pembayaran lama tetap ada di histori)
        $this->assertEquals(2, $this->registration1->payments()->count());

        $latestPayment = $this->registration1->payments()->latest('id')->first();
        $this->assertEquals(Payment::STATUS_MENUNGGU_VERIFIKASI, $latestPayment->status);
        $this->assertNull($latestPayment->rejection_reason);

        // Pastikan halaman status pendaftaran kini menampilkan pesan menunggu verifikasi
        $resStatus = $this->actingAs($this->jamaah1)->get(route('jamaah.my-registration'));
        $resStatus->assertOk();
        $resStatus->assertSee('Pembayaran sedang menunggu verifikasi Admin.');
    }

    public function test_admin_approves_payment_sets_verified_and_clears_active_rejection_reason()
    {
        $payment = Payment::create([
            'registration_id' => $this->registration1->id,
            'type' => Payment::TYPE_DP,
            'amount' => 5000000,
            'proof_file' => 'payments/dp/proof_valid.jpg',
            'status' => Payment::STATUS_MENUNGGU_VERIFIKASI,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.payments.verify', $payment), [
            'action' => 'approve',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $payment->refresh();
        $this->assertEquals(Payment::STATUS_DISETUJUI, $payment->status);
        $this->assertNull($payment->rejection_reason);
        $this->assertEquals($this->admin->id, $payment->verified_by);
        $this->assertNotNull($payment->verified_at);

        $this->registration1->refresh();
        $this->assertEquals(Registration::STATUS_JAMAAH, $this->registration1->status);

        // Halaman Jamaah harus menampilkan tanda verifikasi berhasil
        $resStatus = $this->actingAs($this->jamaah1)->get(route('jamaah.my-registration'));
        $resStatus->assertOk();
        $resStatus->assertSee('Pembayaran telah diverifikasi');
    }

    public function test_rejection_reason_of_jamaah_a_does_not_leak_to_jamaah_b()
    {
        $reasonA = 'Khusus Jamaah A: Bukti mutasi tidak cocok.';

        Payment::create([
            'registration_id' => $this->registration1->id,
            'type' => Payment::TYPE_DP,
            'amount' => 5000000,
            'proof_file' => 'payments/dp/proof_a.jpg',
            'status' => Payment::STATUS_DITOLAK,
            'rejection_reason' => $reasonA,
            'rejected_by' => $this->admin->id,
            'rejected_at' => now(),
        ]);

        $this->registration1->update(['status' => Registration::STATUS_MENUNGGU_PEMBAYARAN_DP]);

        // Jamaah B membuka halaman statusnya
        $resJamaahB = $this->actingAs($this->jamaah2)->get(route('jamaah.my-registration'));
        $resJamaahB->assertOk();
        $resJamaahB->assertDontSee($reasonA);

        // Jamaah B membuka halaman DP
        $resDpB = $this->actingAs($this->jamaah2)->get(route('jamaah.payment.dp'));
        $resDpB->assertOk();
        $resDpB->assertDontSee($reasonA);
        $resDpB->assertDontSee('Pembayaran DP Sebelumnya Ditolak');
    }

    public function test_admin_rejects_pelunasan_payment_and_jamaah_can_reupload()
    {
        // Set registrasi 1 menjadi jamaah resmi
        $this->registration1->update(['status' => Registration::STATUS_JAMAAH]);

        $pelunasan = Payment::create([
            'registration_id' => $this->registration1->id,
            'type' => Payment::TYPE_PELUNASAN,
            'amount' => 10000000,
            'proof_file' => 'payments/pelunasan/proof_pelunasan.jpg',
            'status' => Payment::STATUS_MENUNGGU_VERIFIKASI,
        ]);

        $reason = 'Nominal transfer di struk (Rp 5.000.000) tidak sesuai dengan input sistem (Rp 10.000.000).';

        $response = $this->actingAs($this->admin)->post(route('admin.payments.verify', $pelunasan), [
            'action' => 'reject',
            'rejection_reason' => $reason,
        ]);

        $response->assertRedirect();
        $pelunasan->refresh();
        $this->assertEquals(Payment::STATUS_DITOLAK, $pelunasan->status);
        $this->assertEquals($reason, $pelunasan->rejection_reason);

        // Halaman Jamaah melihat notifikasi penolakan pelunasan
        $resStatus = $this->actingAs($this->jamaah1)->get(route('jamaah.my-registration'));
        $resStatus->assertOk();
        $resStatus->assertSee('Pembayaran Ditolak');
        $resStatus->assertSee($reason);
        $resStatus->assertSee('Upload Bukti Pembayaran Pelunasan');

        // Form pelunasan menampilkan pesan penolakan
        $resPelunasan = $this->actingAs($this->jamaah1)->get(route('jamaah.payment.pelunasan'));
        $resPelunasan->assertOk();
        $resPelunasan->assertSee('Setoran Pelunasan Sebelumnya Ditolak');
        $resPelunasan->assertSee($reason);

        // Jamaah upload ulang bukti pelunasan
        $newFile = UploadedFile::fake()->image('bukti_pelunasan_fix.jpg', 600, 800);
        $resStore = $this->actingAs($this->jamaah1)->post(route('jamaah.payment.pelunasan.store'), [
            'amount' => 5000000,
            'proof_file' => $newFile,
        ]);

        $resStore->assertRedirect(route('jamaah.my-registration'));
        $this->assertEquals(2, $this->registration1->payments()->where('type', Payment::TYPE_PELUNASAN)->count());
    }
}
