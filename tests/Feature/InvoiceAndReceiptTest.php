<?php

namespace Tests\Feature;

use App\Helpers\TerbilangHelper;
use App\Models\Invoice;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\RegistrationCancellation;
use App\Models\RegistrationMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceAndReceiptTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $jamaah1;
    private User $jamaah2;
    private Package $package;
    private Registration $reg1;
    private Registration $reg2;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Admin
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@zeintour.com',
            'name' => 'Admin Keuangan',
        ]);

        // 2. Jamaah 1
        $this->jamaah1 = User::factory()->create([
            'role' => 'jamaah',
            'email' => 'jamaah1@test.com',
            'name' => 'AHMAD FARID KAMALUDIN',
            'phone' => '081234567890',
            'phone_verified_at' => now(),
        ]);

        // 3. Jamaah 2
        $this->jamaah2 = User::factory()->create([
            'role' => 'jamaah',
            'email' => 'jamaah2@test.com',
            'name' => 'SITI NURHALIZA',
            'phone' => '081298765432',
            'phone_verified_at' => now(),
        ]);

        // 4. Paket
        $this->package = Package::create([
            'name' => 'Paket Umrah VIP Eksekutif Awal Musim (12 Hari)',
            'slug' => 'paket-umrah-vip-eksekutif',
            'price' => 33900000,
            'duration' => 12,
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'quota' => 45,
            'facilities' => "Hotel Bintang 5 Mekkah & Madinah\nTiket Pesawat PP Saudia Airlines\nVisa Umrah & Asuransi",
            'status' => 'aktif',
        ]);

        // 5. Pendaftaran Jamaah 1
        $this->reg1 = Registration::create([
            'user_id' => $this->jamaah1->id,
            'package_id' => $this->package->id,
            'status' => 'jamaah',
        ]);

        RegistrationMember::create([
            'registration_id' => $this->reg1->id,
            'name' => $this->jamaah1->name,
            'relationship' => 'diri_sendiri',
            'nik' => '3204123456780001',
            'no_kk' => '3204123456780000',
            'document_status' => 'disetujui',
        ]);

        Invoice::create([
            'registration_id' => $this->reg1->id,
            'invoice_number' => 'INV-2026-00001',
            'total_price' => 33900000,
            'total_paid' => 10000000,
            'remaining_balance' => 23900000,
            'due_date' => now()->addDays(14),
        ]);

        // 6. Pendaftaran Jamaah 2
        $this->reg2 = Registration::create([
            'user_id' => $this->jamaah2->id,
            'package_id' => $this->package->id,
            'status' => 'menunggu_pembayaran_dp',
        ]);

        RegistrationMember::create([
            'registration_id' => $this->reg2->id,
            'name' => $this->jamaah2->name,
            'relationship' => 'diri_sendiri',
            'nik' => '3204123456780002',
            'no_kk' => '3204123456780000',
            'document_status' => 'disetujui',
        ]);

        Invoice::create([
            'registration_id' => $this->reg2->id,
            'invoice_number' => 'INV-2026-00002',
            'total_price' => 33900000,
            'total_paid' => 0,
            'remaining_balance' => 33900000,
            'due_date' => now()->addDays(7),
        ]);
    }

    public function test_terbilang_helper_converts_various_numbers_accurately()
    {
        $this->assertEquals('Nol Rupiah', TerbilangHelper::terbilang(0));
        $this->assertEquals('Sepuluh Juta Rupiah', TerbilangHelper::terbilang(10000000));
        $this->assertEquals('Tiga Puluh Tiga Juta Sembilan Ratus Ribu Rupiah', TerbilangHelper::terbilang(33900000));
        $this->assertEquals('Dua Puluh Tiga Juta Sembilan Ratus Ribu Rupiah', TerbilangHelper::terbilang(23900000));
        $this->assertEquals('Seratus Lima Puluh Juta Rupiah', TerbilangHelper::terbilang(150000000));
    }

    public function test_jamaah_can_view_own_invoice_with_accurate_data_and_persistent_number()
    {
        $response = $this->actingAs($this->jamaah1)->get(route('documents.invoice', $this->reg1));
        $response->assertOk();

        // Verifikasi konten Invoice
        $response->assertSee('INVOICE');
        $response->assertSee('INV-2026-00001');
        $response->assertSee('PT. ZEIN INTERNASIONAL');
        $response->assertSee('AHMAD FARID KAMALUDIN');
        $response->assertSee('Paket Umrah VIP Eksekutif Awal Musim (12 Hari)');
        $response->assertSee('33.900.000');
        $response->assertSee('10.000.000');
        $response->assertSee('23.900.000');
        $response->assertSee(strtoupper('Dua Puluh Tiga Juta Sembilan Ratus Ribu Rupiah'));
        $response->assertSee('132-0025416-463');
        $response->assertSee('FITRIANI, SE.');

        // Refresh halaman tidak mengubah nomor invoice
        $response2 = $this->actingAs($this->jamaah1)->get(route('documents.invoice', $this->reg1));
        $response2->assertOk();
        $response2->assertSee('INV-2026-00001');
    }

    public function test_jamaah_can_view_receipt_for_approved_payment_with_persistent_number()
    {
        $payment = Payment::create([
            'registration_id' => $this->reg1->id,
            'type' => Payment::TYPE_DP,
            'amount' => 10000000,
            'proof_file' => 'payments/proof_dp.jpg',
            'status' => Payment::STATUS_DISETUJUI,
            'receipt_number' => 'KWT-2026-00001',
            'verified_at' => now(),
            'verified_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->jamaah1)->get(route('documents.receipt', $payment));
        $response->assertOk();

        // Verifikasi konten Kwitansi
        $response->assertSee('KWITANSI');
        $response->assertSee('KWT-2026-00001');
        $response->assertSee('PT. ZEIN INTERNASIONAL');
        $response->assertSee('AHMAD FARID KAMALUDIN');
        $response->assertSee('SEPULUH JUTA RUPIAH');
        $response->assertSee('Rp 10.000.000');
        $response->assertSee('Paket Umrah VIP Eksekutif Awal Musim (12 Hari)');
        $response->assertSee('Copy'); // 2-ply copy badge

        // Refresh tidak mengubah nomor kwitansi
        $response2 = $this->actingAs($this->jamaah1)->get(route('documents.receipt', $payment));
        $response2->assertOk();
        $response2->assertSee('KWT-2026-00001');
    }

    public function test_jamaah_cannot_view_receipt_for_pending_or_rejected_payment()
    {
        // 1. Pembayaran Pending
        $pendingPayment = Payment::create([
            'registration_id' => $this->reg1->id,
            'type' => Payment::TYPE_PELUNASAN,
            'amount' => 5000000,
            'proof_file' => 'payments/proof_pending.jpg',
            'status' => Payment::STATUS_MENUNGGU_VERIFIKASI,
        ]);

        $resPending = $this->actingAs($this->jamaah1)->get(route('documents.receipt', $pendingPayment));
        $resPending->assertRedirect();
        $resPending->assertSessionHas('error');

        // 2. Pembayaran Ditolak
        $rejectedPayment = Payment::create([
            'registration_id' => $this->reg1->id,
            'type' => Payment::TYPE_PELUNASAN,
            'amount' => 5000000,
            'proof_file' => 'payments/proof_rejected.jpg',
            'status' => Payment::STATUS_DITOLAK,
            'rejection_reason' => 'Struk transfer tidak jelas.',
        ]);

        $resRejected = $this->actingAs($this->jamaah1)->get(route('documents.receipt', $rejectedPayment));
        $resRejected->assertRedirect();
        $resRejected->assertSessionHas('error');
    }

    public function test_jamaah_cannot_view_another_users_invoice_or_receipt()
    {
        $payment2 = Payment::create([
            'registration_id' => $this->reg2->id,
            'type' => Payment::TYPE_DP,
            'amount' => 10000000,
            'proof_file' => 'payments/proof2.jpg',
            'status' => Payment::STATUS_DISETUJUI,
            'receipt_number' => 'KWT-2026-00002',
            'verified_at' => now(),
            'verified_by' => $this->admin->id,
        ]);

        // Jamaah 1 mencoba akses invoice Jamaah 2 -> 403 Forbidden
        $resInvoice = $this->actingAs($this->jamaah1)->get(route('documents.invoice', $this->reg2));
        $resInvoice->assertForbidden();

        // Jamaah 1 mencoba akses kwitansi Jamaah 2 -> 403 Forbidden
        $resReceipt = $this->actingAs($this->jamaah1)->get(route('documents.receipt', $payment2));
        $resReceipt->assertForbidden();
    }

    public function test_admin_can_view_any_invoice_and_approved_receipt()
    {
        $payment = Payment::create([
            'registration_id' => $this->reg1->id,
            'type' => Payment::TYPE_DP,
            'amount' => 10000000,
            'proof_file' => 'payments/proof_dp.jpg',
            'status' => Payment::STATUS_DISETUJUI,
            'receipt_number' => 'KWT-2026-00001',
            'verified_at' => now(),
            'verified_by' => $this->admin->id,
        ]);

        // Admin akses Invoice Jamaah 1
        $resInvoice = $this->actingAs($this->admin)->get(route('documents.invoice', $this->reg1));
        $resInvoice->assertOk();
        $resInvoice->assertSee('INV-2026-00001');

        // Admin akses Kwitansi Jamaah 1
        $resReceipt = $this->actingAs($this->admin)->get(route('documents.receipt', $payment));
        $resReceipt->assertOk();
        $resReceipt->assertSee('KWT-2026-00001');
    }

    public function test_cancelled_registration_retains_invoice_and_receipt_history()
    {
        $payment = Payment::create([
            'registration_id' => $this->reg1->id,
            'type' => Payment::TYPE_DP,
            'amount' => 10000000,
            'proof_file' => 'payments/proof_dp.jpg',
            'status' => Payment::STATUS_DISETUJUI,
            'receipt_number' => 'KWT-2026-00001',
            'verified_at' => now(),
            'verified_by' => $this->admin->id,
        ]);

        // Pembatalan disetujui
        RegistrationCancellation::create([
            'registration_id' => $this->reg1->id,
            'user_id' => $this->jamaah1->id,
            'reason' => 'Batal dinas.',
            'status' => RegistrationCancellation::STATUS_APPROVED,
            'processed_by' => $this->admin->id,
            'processed_at' => now(),
        ]);
        $this->reg1->update([
            'status' => Registration::STATUS_DIBATALKAN,
            'cancellation_status' => RegistrationCancellation::STATUS_APPROVED,
        ]);

        // Invoice dan Kwitansi tetap dapat diakses sebagai histori transaksi audit
        $resInvoice = $this->actingAs($this->jamaah1)->get(route('documents.invoice', $this->reg1));
        $resInvoice->assertOk();
        $resInvoice->assertSee('INV-2026-00001');

        $resReceipt = $this->actingAs($this->jamaah1)->get(route('documents.receipt', $payment));
        $resReceipt->assertOk();
        $resReceipt->assertSee('KWT-2026-00001');
    }

    public function test_excel_document_service_generates_valid_invoice_xlsx_from_master_template()
    {
        $service = app(\App\Services\ExcelDocumentService::class);
        $filePath = $service->generateInvoice($this->reg1);

        $this->assertFileExists($filePath);
        $this->assertStringContainsString('Invoice_INV-2026-00001.xlsx', $filePath);
        $this->assertGreaterThan(50000, filesize($filePath)); // Ukuran template asli dengan logo
    }

    public function test_excel_document_service_generates_valid_receipt_xlsx_from_master_template()
    {
        $payment = Payment::create([
            'registration_id' => $this->reg1->id,
            'type' => Payment::TYPE_DP,
            'amount' => 10000000,
            'proof_file' => 'payments/proof_dp.jpg',
            'status' => Payment::STATUS_DISETUJUI,
            'receipt_number' => 'KWT-2026-00001',
            'verified_at' => now(),
            'verified_by' => $this->admin->id,
        ]);

        $service = app(\App\Services\ExcelDocumentService::class);
        $filePath = $service->generateReceipt($payment);

        $this->assertFileExists($filePath);
        $this->assertStringContainsString('Kwitansi_KWT-2026-00001.xlsx', $filePath);
        $this->assertGreaterThan(50000, filesize($filePath));
    }

    public function test_admin_approving_payment_automatically_generates_receipt_and_updates_invoice_xlsx()
    {
        $payment = Payment::create([
            'registration_id' => $this->reg1->id,
            'type' => Payment::TYPE_DP,
            'amount' => 10000000,
            'proof_file' => 'payments/proof_dp.jpg',
            'status' => Payment::STATUS_MENUNGGU_VERIFIKASI,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.payments.verify', $payment), [
            'action' => 'approve',
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $payment->refresh();
        $this->assertEquals(Payment::STATUS_DISETUJUI, $payment->status);
        $this->assertNotNull($payment->receipt_number);

        $service = app(\App\Services\ExcelDocumentService::class);
        $receiptPath = $service->getOrGenerateReceiptPath($payment);
        $this->assertFileExists($receiptPath);

        $invoicePath = $service->getOrGenerateInvoicePath($this->reg1);
        $this->assertFileExists($invoicePath);
    }

    public function test_jamaah_and_admin_can_download_invoice_and_receipt_xlsx()
    {
        $payment = Payment::create([
            'registration_id' => $this->reg1->id,
            'type' => Payment::TYPE_DP,
            'amount' => 10000000,
            'proof_file' => 'payments/proof_dp.jpg',
            'status' => Payment::STATUS_DISETUJUI,
            'receipt_number' => 'KWT-2026-00001',
            'verified_at' => now(),
            'verified_by' => $this->admin->id,
        ]);

        // Jamaah unduh invoice miliknya
        $resInvJamaah = $this->actingAs($this->jamaah1)->get(route('documents.invoice.download', $this->reg1));
        $resInvJamaah->assertOk();
        $resInvJamaah->assertHeader('content-disposition');

        // Jamaah unduh kwitansi miliknya
        $resRecJamaah = $this->actingAs($this->jamaah1)->get(route('documents.receipt.download', $payment));
        $resRecJamaah->assertOk();
        $resRecJamaah->assertHeader('content-disposition');

        // Admin unduh invoice & kwitansi
        $resInvAdmin = $this->actingAs($this->admin)->get(route('documents.invoice.download', $this->reg1));
        $resInvAdmin->assertOk();

        $resRecAdmin = $this->actingAs($this->admin)->get(route('documents.receipt.download', $payment));
        $resRecAdmin->assertOk();
    }

    public function test_jamaah_cannot_download_another_users_invoice_or_receipt_xlsx()
    {
        $payment2 = Payment::create([
            'registration_id' => $this->reg2->id,
            'type' => Payment::TYPE_DP,
            'amount' => 10000000,
            'proof_file' => 'payments/proof2.jpg',
            'status' => Payment::STATUS_DISETUJUI,
            'receipt_number' => 'KWT-2026-00002',
            'verified_at' => now(),
            'verified_by' => $this->admin->id,
        ]);

        // Jamaah 1 coba unduh invoice Jamaah 2 -> 403 Forbidden
        $resInv = $this->actingAs($this->jamaah1)->get(route('documents.invoice.download', $this->reg2));
        $resInv->assertForbidden();

        // Jamaah 1 coba unduh kwitansi Jamaah 2 -> 403 Forbidden
        $resRec = $this->actingAs($this->jamaah1)->get(route('documents.receipt.download', $payment2));
        $resRec->assertForbidden();
    }

    public function test_jamaah_cannot_download_unapproved_receipt_xlsx()
    {
        $pendingPayment = Payment::create([
            'registration_id' => $this->reg1->id,
            'type' => Payment::TYPE_PELUNASAN,
            'amount' => 5000000,
            'proof_file' => 'payments/proof_pending.jpg',
            'status' => Payment::STATUS_MENUNGGU_VERIFIKASI,
        ]);

        $res = $this->actingAs($this->jamaah1)->get(route('documents.receipt.download', $pendingPayment));
        $res->assertRedirect();
        $res->assertSessionHas('error');
    }

    public function test_admin_can_view_invoice_section_in_user_detail_page()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.show', $this->jamaah1));
        $response->assertOk();

        $response->assertSee('Invoice Tagihan Jamaah');
        $response->assertSee('INV-2026-00001');
        $response->assertSee('AHMAD FARID KAMALUDIN');
        $response->assertSee('Paket Umrah VIP Eksekutif Awal Musim (12 Hari)');
        $response->assertSee('33.900.000');
        $response->assertSee('10.000.000');
        $response->assertSee('23.900.000');
        $response->assertSee('Lihat Invoice');
        $response->assertSee('Cetak');
        $response->assertSee('Download');
    }

    public function test_viewing_invoice_auto_generates_physical_xlsx_file_if_not_present()
    {
        $invoiceNumber = $this->reg1->invoice->invoice_number;
        $cleanNumber = str_replace(['/', '\\', ' '], '_', $invoiceNumber);
        $expectedFilePath = storage_path("app/documents/invoices/Invoice_{$cleanNumber}.xlsx");

        // Hapus file jika sebelumnya ada untuk menguji auto-generate
        if (file_exists($expectedFilePath)) {
            unlink($expectedFilePath);
        }

        $this->assertFileDoesNotExist($expectedFilePath);

        // Admin buka halaman invoice (Lihat Invoice)
        $response = $this->actingAs($this->admin)->get(route('documents.invoice', $this->reg1));
        $response->assertOk();

        // File fisik Excel harus otomatis ter-generate dan tersimpan di storage/app/documents/invoices/
        $this->assertFileExists($expectedFilePath);
    }
}
