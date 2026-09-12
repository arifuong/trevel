<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Package;
use App\Models\Registration;
use App\Models\RegistrationMember;
use App\Models\User;
use App\Services\BookingStatusService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompletedStatusWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_step_number_for_selesai_is_9_and_marks_is_completed(): void
    {
        $user = User::factory()->create(['role' => 'jamaah']);
        $package = Package::create([
            'name' => 'Paket Selesai Test ' . uniqid(),
            'price' => 35000000,
            'departure_date' => now()->subDays(10),
            'duration' => 12,
            'quota' => 45,
            'facilities' => 'Full Board Hotel Bintang 5',
            'status' => Package::STATUS_AKTIF,
        ]);

        $registration = Registration::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'status' => Registration::STATUS_SELESAI,
        ]);

        $this->assertEquals(9, $registration->step_number);
        $this->assertEquals('Selesai', $registration->status_label);
        $this->assertTrue($registration->isCompleted());
        $this->assertFalse($registration->isActive());
    }

    public function test_admin_can_mark_individual_registration_completed(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'jamaah']);
        $package = Package::create([
            'name' => 'Paket Test ' . uniqid(),
            'price' => 35000000,
            'departure_date' => now()->subDays(5),
            'duration' => 12,
            'quota' => 40,
            'facilities' => 'Full Board',
            'status' => Package::STATUS_AKTIF,
        ]);

        $registration = Registration::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'status' => Registration::STATUS_BERANGKAT,
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.registrations.complete', $registration));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals(Registration::STATUS_SELESAI, $registration->fresh()->status);

        $this->assertDatabaseHas('audit_logs', [
            'action' => AuditLog::ACTION_REGISTRATION_COMPLETED,
            'user_id' => $admin->id,
            'auditable_type' => Registration::class,
            'auditable_id' => $registration->id,
        ]);
    }

    public function test_cannot_mark_completed_if_not_in_berangkat_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'jamaah']);
        $package = Package::create([
            'name' => 'Paket Test ' . uniqid(),
            'price' => 35000000,
            'departure_date' => now()->addDays(30),
            'duration' => 12,
            'quota' => 40,
            'facilities' => 'Full Board',
            'status' => Package::STATUS_AKTIF,
        ]);

        // Registration still in 'lunas' status
        $registration = Registration::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'status' => Registration::STATUS_LUNAS,
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.registrations.complete', $registration));

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertEquals(Registration::STATUS_LUNAS, $registration->fresh()->status);
        $this->assertDatabaseMissing('audit_logs', [
            'auditable_id' => $registration->id,
            'action' => AuditLog::ACTION_REGISTRATION_COMPLETED,
        ]);
    }

    public function test_jamaah_cannot_mark_registration_completed(): void
    {
        $jamaah = User::factory()->create(['role' => 'jamaah']);
        $package = Package::create([
            'name' => 'Paket Test ' . uniqid(),
            'price' => 35000000,
            'departure_date' => now()->subDays(5),
            'duration' => 12,
            'quota' => 40,
            'facilities' => 'Full Board',
            'status' => Package::STATUS_AKTIF,
        ]);

        $registration = Registration::create([
            'user_id' => $jamaah->id,
            'package_id' => $package->id,
            'status' => Registration::STATUS_BERANGKAT,
        ]);

        $response = $this->actingAs($jamaah)
            ->post(route('admin.registrations.complete', $registration));

        $response->assertRedirect(route('admin.login'));
        $this->assertEquals(Registration::STATUS_BERANGKAT, $registration->fresh()->status);

        $jsonResponse = $this->actingAs($jamaah)
            ->postJson(route('admin.registrations.complete', $registration));

        $jsonResponse->assertForbidden();
        $this->assertEquals(Registration::STATUS_BERANGKAT, $registration->fresh()->status);
    }

    public function test_admin_can_complete_departure_in_bulk(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user1 = User::factory()->create(['role' => 'jamaah']);
        $user2 = User::factory()->create(['role' => 'jamaah']);
        $user3 = User::factory()->create(['role' => 'jamaah']);

        $package = Package::create([
            'name' => 'Paket Bulk Test ' . uniqid(),
            'price' => 35000000,
            'departure_date' => now()->subDays(2),
            'duration' => 12,
            'quota' => 40,
            'facilities' => 'Full Board',
            'status' => Package::STATUS_AKTIF,
        ]);

        $reg1 = Registration::create([
            'user_id' => $user1->id,
            'package_id' => $package->id,
            'status' => Registration::STATUS_BERANGKAT,
        ]);

        $reg2 = Registration::create([
            'user_id' => $user2->id,
            'package_id' => $package->id,
            'status' => Registration::STATUS_BERANGKAT,
        ]);

        $reg3 = Registration::create([
            'user_id' => $user3->id,
            'package_id' => $package->id,
            'status' => Registration::STATUS_MENUNGGU_KELENGKAPAN_KEBERANGKATAN,
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.departures.complete', $package));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals(Registration::STATUS_SELESAI, $reg1->fresh()->status);
        $this->assertEquals(Registration::STATUS_SELESAI, $reg2->fresh()->status);
        $this->assertEquals(Registration::STATUS_MENUNGGU_KELENGKAPAN_KEBERANGKATAN, $reg3->fresh()->status);

        $this->assertDatabaseHas('audit_logs', [
            'action' => AuditLog::ACTION_DEPARTURE_COMPLETED,
            'user_id' => $admin->id,
            'auditable_type' => Package::class,
            'auditable_id' => $package->id,
        ]);
    }

    public function test_booking_status_service_never_modifies_selesai_status(): void
    {
        $user = User::factory()->create(['role' => 'jamaah']);
        $package = Package::create([
            'name' => 'Paket Test ' . uniqid(),
            'price' => 35000000,
            'departure_date' => now()->subDays(20),
            'duration' => 12,
            'quota' => 40,
            'facilities' => 'Full Board',
            'status' => Package::STATUS_AKTIF,
        ]);

        $registration = Registration::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'status' => Registration::STATUS_SELESAI,
        ]);

        $service = app(BookingStatusService::class);
        $result = $service->evaluateStatus($registration);

        $this->assertEquals(Registration::STATUS_SELESAI, $result);
        $this->assertEquals(Registration::STATUS_SELESAI, $registration->fresh()->status);
    }

    public function test_jamaah_status_view_shows_9_steps_and_celebratory_banner(): void
    {
        $user = User::factory()->create(['role' => 'jamaah', 'name' => 'Ahmad Test']);
        $package = Package::create([
            'name' => 'Paket Umrah Berkah ' . uniqid(),
            'price' => 35000000,
            'departure_date' => now()->subDays(15),
            'duration' => 12,
            'quota' => 40,
            'facilities' => 'Full Board Hotel Bintang 5',
            'status' => Package::STATUS_AKTIF,
        ]);

        $registration = Registration::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'status' => Registration::STATUS_SELESAI,
        ]);

        RegistrationMember::create([
            'registration_id' => $registration->id,
            'name' => 'Ahmad Test',
            'nik' => '3201010101900001',
            'no_kk' => '3201010101900001',
            'relationship' => 'diri_sendiri',
            'document_status' => RegistrationMember::DOC_STATUS_DISETUJUI,
        ]);

        $response = $this->actingAs($user)
            ->get(route('jamaah.registration.status'));

        $response->assertOk();
        $response->assertSee('Langkah 9 dari 9');
        $response->assertSee('Semoga Menjadi Ibadah yang Mabrur');
        $response->assertSee('Perjalanan Ibadah Telah Selesai');
        $response->assertDontSee('type="file" name="file"', false);
        $response->assertDontSee('Formulir Unggah Ulang Dokumen');
    }

    public function test_dashboard_excludes_selesai_from_active_and_counts_in_total_selesai(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user1 = User::factory()->create(['role' => 'jamaah']);
        $user2 = User::factory()->create(['role' => 'jamaah']);

        $package = Package::create([
            'name' => 'Paket Dashboard Test ' . uniqid(),
            'price' => 35000000,
            'departure_date' => now()->addDays(20),
            'duration' => 12,
            'quota' => 40,
            'facilities' => 'Full Board',
            'status' => Package::STATUS_AKTIF,
        ]);

        // Active registration
        Registration::create([
            'user_id' => $user1->id,
            'package_id' => $package->id,
            'status' => Registration::STATUS_BERANGKAT,
        ]);

        // Completed registration
        Registration::create([
            'user_id' => $user2->id,
            'package_id' => $package->id,
            'status' => Registration::STATUS_SELESAI,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewHas('totalPendaftaran', 1);
        $response->assertViewHas('totalSelesai', 1);
        $response->assertSee('Selesai Ibadah');
    }
}
