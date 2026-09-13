<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\RegistrationMember;
use App\Models\User;
use App\Services\PeriodFilterService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class AdminReportsAndPeriodFilterTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $jamaah;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'name' => 'Admin Test',
        ]);

        $this->jamaah = User::factory()->create([
            'role' => 'jamaah',
            'name' => 'Jamaah Test',
        ]);
    }

    protected function createPackage(array $attributes = []): Package
    {
        return Package::create(array_merge([
            'name' => 'Paket Umrah Eksklusif 1448H',
            'slug' => 'paket-umrah-eksklusif-' . uniqid(),
            'price' => 35000000,
            'duration' => 12,
            'departure_date' => now()->format('Y-m-d'),
            'facilities' => 'Hotel Bintang 5',
            'quota' => 45,
            'status' => 'aktif',
        ], $attributes));
    }

    protected function createMember(Registration $registration, array $attributes = []): RegistrationMember
    {
        return RegistrationMember::create(array_merge([
            'registration_id' => $registration->id,
            'name' => 'Jamaah Member ' . uniqid(),
            'nik' => '320123456789' . rand(1000, 9999),
            'no_kk' => '3201234567890001',
            'gender' => 'L',
            'birth_date' => '1990-01-01',
            'relationship' => 'self',
            'document_status' => RegistrationMember::DOC_STATUS_DISETUJUI,
        ], $attributes));
    }

    protected function createInvoice(Registration $registration, array $attributes = []): Invoice
    {
        return Invoice::create(array_merge([
            'registration_id' => $registration->id,
            'total_price' => 35000000,
            'total_paid' => 10000000,
            'remaining_balance' => 25000000,
            'due_date' => now()->addDays(14)->format('Y-m-d'),
        ], $attributes));
    }

    // ==========================================
    // 1. PeriodFilterService Unit/Functional Tests
    // ==========================================

    public function test_period_filter_service_defaults_to_current_month(): void
    {
        $service = new PeriodFilterService();
        $request = Request::create('/admin/reports', 'GET');

        $result = $service->parseRequest($request);

        $this->assertEquals('month', $result['period']);
        $this->assertEquals(now()->month, $result['selectedMonth']);
        $this->assertEquals(now()->year, $result['selectedYear']);
        $this->assertEquals(now()->startOfMonth()->startOfDay()->toDateTimeString(), $result['start_date']->toDateTimeString());
        $this->assertEquals(now()->endOfMonth()->endOfDay()->toDateTimeString(), $result['end_date']->toDateTimeString());
        $this->assertEquals('Mingguan', $result['granularity_label']);
    }

    public function test_period_filter_service_handles_week_period(): void
    {
        $service = new PeriodFilterService();
        $request = Request::create('/admin/reports', 'GET', [
            'period' => 'week',
            'week_date' => '2026-09-10',
        ]);

        $result = $service->parseRequest($request);

        $this->assertEquals('week', $result['period']);
        $this->assertEquals('2026-09-07', $result['weekDate']);
        $this->assertEquals('2026-09-07 00:00:00', $result['start_date']->toDateTimeString()); // Monday
        $this->assertEquals('2026-09-13 23:59:59', $result['end_date']->toDateTimeString()); // Sunday
        $this->assertEquals('Harian', $result['granularity_label']);

        $buckets = $service->getBuckets($result['start_date'], $result['end_date'], 'week');
        $this->assertCount(7, $buckets);
    }

    public function test_period_filter_service_handles_year_period(): void
    {
        $service = new PeriodFilterService();
        $request = Request::create('/admin/reports', 'GET', [
            'period' => 'year',
            'year' => 2026,
        ]);

        $result = $service->parseRequest($request);

        $this->assertEquals('year', $result['period']);
        $this->assertEquals(2026, $result['selectedYear']);
        $this->assertEquals('2026-01-01 00:00:00', $result['start_date']->toDateTimeString());
        $this->assertEquals('2026-12-31 23:59:59', $result['end_date']->toDateTimeString());
        $this->assertEquals('Bulanan', $result['granularity_label']);

        $buckets = $service->getBuckets($result['start_date'], $result['end_date'], 'year');
        $this->assertCount(12, $buckets);
    }

    public function test_period_filter_service_handles_custom_period(): void
    {
        $service = new PeriodFilterService();
        $request = Request::create('/admin/reports', 'GET', [
            'period' => 'custom',
            'start_date' => '2026-08-01',
            'end_date' => '2026-08-15',
        ]);

        $result = $service->parseRequest($request);

        $this->assertEquals('custom', $result['period']);
        $this->assertEquals('2026-08-01 00:00:00', $result['start_date']->toDateTimeString());
        $this->assertEquals('2026-08-15 23:59:59', $result['end_date']->toDateTimeString());

        $buckets = $service->getBuckets($result['start_date'], $result['end_date'], 'custom');
        $this->assertCount(15, $buckets);
    }

    // ==========================================
    // 2. Navigation Verification (1 Consolidated "Laporan" Menu)
    // ==========================================

    public function test_admin_sidebar_and_drawer_render_consolidated_report_menu(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee(route('admin.reports.index'));
        $response->assertSee('Laporan');
        $response->assertSee('Laporan &amp; Rekap', false);
    }

    // ==========================================
    // 3. Consolidated Reports Page & Tab Tests
    // ==========================================

    public function test_admin_can_view_laporan_jamaah_tab_by_default(): void
    {
        $pkg = $this->createPackage();

        $reg1 = Registration::create([
            'user_id' => $this->jamaah->id,
            'package_id' => $pkg->id,
            'status' => Registration::STATUS_MENUNGGU_PEMBAYARAN_DP,
            'created_at' => now(),
        ]);
        $this->createMember($reg1, [
            'name' => 'Member One',
        ]);

        // Default route without ?tab should load jamaah
        $response = $this->actingAs($this->admin)->get(route('admin.reports.index', [
            'period' => 'month',
            'month' => now()->month,
            'year' => now()->year,
        ]));

        $response->assertOk();
        $response->assertSee('Laporan &amp; Rekapitulasi', false);
        $response->assertSee('Laporan Jamaah');
        $response->assertSee('Laporan Pembayaran');
        $response->assertSee('Laporan Piutang');
        $response->assertSee('Laporan Keberangkatan');
        $response->assertSee('Pendaftaran Baru');
        $response->assertSee('Total Jamaah (Pax)');
        $response->assertSee($this->jamaah->name);
        $response->assertSee($reg1->registration_number);
        $response->assertSee($pkg->name);
    }

    public function test_admin_can_fetch_tab_content_via_ajax(): void
    {
        $pkg = $this->createPackage();

        $reg1 = Registration::create([
            'user_id' => $this->jamaah->id,
            'package_id' => $pkg->id,
            'status' => Registration::STATUS_MENUNGGU_PEMBAYARAN_DP,
            'created_at' => now(),
        ]);
        $this->createMember($reg1, ['name' => 'Ajax Member']);

        $response = $this->actingAs($this->admin)->get(route('admin.reports.index', [
            'tab' => 'jamaah',
            'period' => 'month',
        ]), ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertOk();
        // AJAX response returns the partial directly
        $response->assertSee('Pendaftaran Baru');
        $response->assertSee($reg1->registration_number);
        $response->assertDontSee('<!DOCTYPE html>');
    }

    public function test_admin_can_export_laporan_jamaah_csv_and_pdf(): void
    {
        $pkg = $this->createPackage();
        $reg = Registration::create([
            'user_id' => $this->jamaah->id,
            'package_id' => $pkg->id,
            'status' => Registration::STATUS_SELESAI,
            'created_at' => now(),
        ]);
        $this->createMember($reg, [
            'name' => 'Haji Jamaah Export',
        ]);

        // CSV Export
        $csvResponse = $this->actingAs($this->admin)->get(route('admin.reports.jamaah.export', [
            'format' => 'csv',
            'period' => 'month',
            'month' => now()->month,
            'year' => now()->year,
        ]));

        $csvResponse->assertOk();
        $this->assertStringContainsString('text/csv', $csvResponse->headers->get('Content-Type'));
        $this->assertStringContainsStringIgnoringCase('Laporan_Pendaftaran_Jamaah', $csvResponse->headers->get('Content-Disposition'));
        $content = $csvResponse->streamedContent();
        $this->assertStringContainsString($this->jamaah->name, $content);
        $this->assertStringContainsString($reg->registration_number, $content);

        // PDF Export
        $pdfResponse = $this->actingAs($this->admin)->get(route('admin.reports.jamaah.export', [
            'format' => 'pdf',
            'period' => 'month',
            'month' => now()->month,
            'year' => now()->year,
        ]));

        $pdfResponse->assertOk();
        $this->assertStringContainsString('application/pdf', $pdfResponse->headers->get('Content-Type'));
    }

    // ==========================================
    // 4. Laporan Pembayaran Tab Tests
    // ==========================================

    public function test_admin_can_view_laporan_pembayaran_tab_and_filters(): void
    {
        $pkg = $this->createPackage();
        $reg = Registration::create([
            'user_id' => $this->jamaah->id,
            'package_id' => $pkg->id,
            'status' => Registration::STATUS_MENUNGGU_VERIFIKASI_PEMBAYARAN_DP,
        ]);
        $this->createInvoice($reg, [
            'total_price' => 35000000,
            'total_paid' => 10000000,
            'remaining_balance' => 25000000,
            'due_date' => now()->addMonth()->format('Y-m-d'),
        ]);

        $payment = Payment::create([
            'registration_id' => $reg->id,
            'type' => Payment::TYPE_DP,
            'amount' => 10000000,
            'proof_file' => 'payment_proofs/test_proof.jpg',
            'status' => Payment::STATUS_DISETUJUI,
            'verified_at' => now(),
            'verified_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.reports.index', [
            'tab' => 'pembayaran',
            'period' => 'month',
            'month' => now()->month,
            'year' => now()->year,
        ]));

        $response->assertOk();
        $response->assertSee('Laporan Pembayaran');
        $response->assertSee('Total Dana Masuk');
        $response->assertSee('Rp 10.000.000');
        $response->assertSee($reg->registration_number);
    }

    public function test_admin_can_export_laporan_pembayaran_csv_and_pdf(): void
    {
        $pkg = $this->createPackage();
        $reg = Registration::create([
            'user_id' => $this->jamaah->id,
            'package_id' => $pkg->id,
            'status' => Registration::STATUS_MENUNGGU_VERIFIKASI_PEMBAYARAN_DP,
        ]);
        $this->createInvoice($reg, [
            'total_price' => 35000000,
            'total_paid' => 15000000,
            'remaining_balance' => 20000000,
            'due_date' => now()->addMonth()->format('Y-m-d'),
        ]);

        $payment = Payment::create([
            'registration_id' => $reg->id,
            'type' => Payment::TYPE_DP,
            'amount' => 15000000,
            'proof_file' => 'payment_proofs/test_proof.jpg',
            'status' => Payment::STATUS_DISETUJUI,
            'verified_at' => now(),
            'verified_by' => $this->admin->id,
        ]);

        // CSV
        $csvResponse = $this->actingAs($this->admin)->get(route('admin.reports.payments.export', [
            'format' => 'csv',
            'period' => 'month',
        ]));
        $csvResponse->assertOk();
        $this->assertStringContainsString('text/csv', $csvResponse->headers->get('Content-Type'));
        $content = $csvResponse->streamedContent();
        $this->assertStringContainsString('15000000', $content);
        $this->assertStringContainsString($reg->registration_number, $content);

        // PDF
        $pdfResponse = $this->actingAs($this->admin)->get(route('admin.reports.payments.export', [
            'format' => 'pdf',
            'period' => 'month',
        ]));
        $pdfResponse->assertOk();
        $this->assertStringContainsString('application/pdf', $pdfResponse->headers->get('Content-Type'));
    }

    // ==========================================
    // 5. Laporan Piutang Tab Tests
    // ==========================================

    public function test_admin_can_view_laporan_piutang_tab(): void
    {
        $pkg = $this->createPackage([
            'departure_date' => now()->format('Y-m-d'),
        ]);
        $reg = Registration::create([
            'user_id' => $this->jamaah->id,
            'package_id' => $pkg->id,
            'status' => Registration::STATUS_CICILAN_PELUNASAN,
        ]);
        $this->createMember($reg, [
            'name' => 'Jamaah Piutang',
        ]);
        $this->createInvoice($reg, [
            'total_price' => 35000000,
            'total_paid' => 10000000,
            'remaining_balance' => 25000000,
            'due_date' => now()->addDays(10)->format('Y-m-d'),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.reports.index', [
            'tab' => 'piutang',
            'period' => 'month',
            'month' => now()->month,
            'year' => now()->year,
        ]));

        $response->assertOk();
        $response->assertSee('Laporan Piutang');
        $response->assertSee('Sisa Piutang Berjalan');
        $response->assertSee('Rp 25.000.000');
        $response->assertSee($this->jamaah->name);
        $response->assertSee($reg->registration_number);
    }

    public function test_admin_can_export_laporan_piutang_csv_and_pdf(): void
    {
        $pkg = $this->createPackage(['departure_date' => now()->format('Y-m-d')]);
        $reg = Registration::create([
            'user_id' => $this->jamaah->id,
            'package_id' => $pkg->id,
            'status' => Registration::STATUS_CICILAN_PELUNASAN,
        ]);
        $this->createMember($reg, ['name' => 'Piutang Export']);
        $this->createInvoice($reg, [
            'total_price' => 35000000,
            'total_paid' => 5000000,
            'remaining_balance' => 30000000,
            'due_date' => now()->addMonth()->format('Y-m-d'),
        ]);

        $csv = $this->actingAs($this->admin)->get(route('admin.reports.receivables.export', ['format' => 'csv']));
        $csv->assertOk();
        $this->assertStringContainsString('30000000', $csv->streamedContent());

        $pdf = $this->actingAs($this->admin)->get(route('admin.reports.receivables.export', ['format' => 'pdf']));
        $pdf->assertOk();
    }

    // ==========================================
    // 6. Laporan Keberangkatan Tab Tests
    // ==========================================

    public function test_admin_can_view_laporan_keberangkatan_tab(): void
    {
        $pkg = $this->createPackage([
            'name' => 'Paket Kloter Madinah',
            'quota' => 50,
            'departure_date' => now()->format('Y-m-d'),
        ]);
        $reg = Registration::create([
            'user_id' => $this->jamaah->id,
            'package_id' => $pkg->id,
            'status' => Registration::STATUS_MENUNGGU_PEMBAYARAN_DP,
        ]);
        $this->createMember($reg, ['name' => 'Jamaah Kloter']);

        $response = $this->actingAs($this->admin)->get(route('admin.reports.index', [
            'tab' => 'keberangkatan',
            'period' => 'month',
            'month' => now()->month,
            'year' => now()->year,
        ]));

        $response->assertOk();
        $response->assertSee('Laporan Keberangkatan');
        $response->assertSee('Paket Kloter Madinah');
        $response->assertSee('50 Kursi');
    }

    public function test_tab_switching_preserves_period_filter(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports.index', [
            'tab' => 'keberangkatan',
            'period' => 'year',
            'year' => 2026,
        ]));

        $response->assertOk();
        $response->assertViewHas('tab', 'keberangkatan');
        $periodData = $response->viewData('periodData');
        $this->assertEquals('year', $periodData['period']);
        $this->assertEquals(2026, $periodData['selectedYear']);
    }

    public function test_admin_can_export_laporan_keberangkatan_csv_and_pdf(): void
    {
        $pkg = $this->createPackage(['name' => 'Paket Export Kloter', 'quota' => 40, 'departure_date' => now()->format('Y-m-d')]);

        $csv = $this->actingAs($this->admin)->get(route('admin.reports.departures.export', ['format' => 'csv']));
        $csv->assertOk();
        $this->assertStringContainsString('Paket Export Kloter', $csv->streamedContent());

        $pdf = $this->actingAs($this->admin)->get(route('admin.reports.departures.export', ['format' => 'pdf']));
        $pdf->assertOk();
    }

    // ==========================================
    // 7. Dashboard Admin Chart Analytics
    // ==========================================

    public function test_admin_dashboard_renders_chart_canvas_and_period_datasets(): void
    {
        $pkg = $this->createPackage();
        $reg = Registration::create([
            'user_id' => $this->jamaah->id,
            'package_id' => $pkg->id,
            'status' => Registration::STATUS_MENUNGGU_VERIFIKASI_PEMBAYARAN_DP,
            'created_at' => now(),
        ]);

        Payment::create([
            'registration_id' => $reg->id,
            'type' => Payment::TYPE_DP,
            'amount' => 12000000,
            'proof_file' => 'payment_proofs/test_proof.jpg',
            'status' => Payment::STATUS_DISETUJUI,
            'verified_at' => now(),
            'verified_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard', [
            'period' => 'week',
            'week_date' => now()->format('Y-m-d'),
        ]));

        $response->assertOk();
        $response->assertViewHas('chartLabels');
        $response->assertViewHas('registrationChartData');
        $response->assertViewHas('paymentChartData');
        $response->assertViewHas('totalPeriodRegistrations', 1);
        $response->assertViewHas('totalPeriodIncome', 12000000.0);

        $response->assertSee('registrationChartDesktop');
        $response->assertSee('paymentChartDesktop');
        $response->assertSee('Tren &amp; Analitik Kinerja', false);
        $response->assertSee('Rp 12.000.000');
    }

    // ==========================================
    // 8. Authorization Check
    // ==========================================

    public function test_jamaah_cannot_access_reports_or_exports(): void
    {
        $this->actingAs($this->jamaah)->get(route('admin.reports.index'))->assertRedirect();
        $this->actingAs($this->jamaah)->get(route('admin.reports.jamaah.export', ['format' => 'csv']))->assertRedirect();
        $this->actingAs($this->jamaah)->get(route('admin.reports.payments.export', ['format' => 'csv']))->assertRedirect();
        $this->actingAs($this->jamaah)->get(route('admin.reports.receivables.export', ['format' => 'csv']))->assertRedirect();
        $this->actingAs($this->jamaah)->get(route('admin.reports.departures.export', ['format' => 'csv']))->assertRedirect();
    }
}