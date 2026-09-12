<?php

namespace Tests\Feature;

use App\Models\DocumentType;
use App\Models\JamaahDocument;
use App\Models\Package;
use App\Models\Registration;
use App\Models\RegistrationMember;
use App\Models\User;
use App\Services\BookingStatusService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DepartureDocumentWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_document_types_contain_departure_phase(): void
    {
        $departureTypes = DocumentType::departurePhase()->get();
        $this->assertCount(3, $departureTypes);
        $this->assertTrue($departureTypes->contains('code', DocumentType::CODE_VISA));
        $this->assertTrue($departureTypes->contains('code', DocumentType::CODE_VAKSIN_MENINGITIS));
        $this->assertTrue($departureTypes->contains('code', DocumentType::CODE_FOTO_VISA));
    }

    public function test_lunas_status_evaluates_to_menunggu_kelengkapan_keberangkatan(): void
    {
        $user = User::factory()->create(['role' => 'jamaah']);
        $package = Package::create([
            'name' => 'Paket Feature Test ' . uniqid(),
            'price' => 35000000,
            'departure_date' => now()->addDays(60),
            'duration' => 12,
            'quota' => 45,
            'facilities' => 'Full Board Hotel Bintang 5',
            'status' => Package::STATUS_AKTIF,
        ]);

        $registration = Registration::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'status' => Registration::STATUS_LUNAS,
        ]);

        RegistrationMember::create([
            'registration_id' => $registration->id,
            'name' => 'Jamaah Test 1',
            'nik' => '3201010101900001',
            'no_kk' => '3201010101900001',
            'relationship' => 'diri_sendiri',
            'document_status' => RegistrationMember::DOC_STATUS_DISETUJUI,
        ]);

        $service = app(BookingStatusService::class);
        $newStatus = $service->evaluateStatus($registration);

        $this->assertEquals(Registration::STATUS_MENUNGGU_KELENGKAPAN_KEBERANGKATAN, $newStatus);
        $this->assertEquals(7, $registration->fresh()->step_number);
    }

    public function test_all_valid_departure_docs_transitions_to_berangkat(): void
    {
        $user = User::factory()->create(['role' => 'jamaah']);
        $package = Package::create([
            'name' => 'Paket Feature Test ' . uniqid(),
            'price' => 35000000,
            'departure_date' => now()->addDays(60),
            'duration' => 12,
            'quota' => 45,
            'facilities' => 'Full Board Hotel Bintang 5',
            'status' => Package::STATUS_AKTIF,
        ]);

        $registration = Registration::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'status' => Registration::STATUS_LUNAS,
        ]);

        $member = RegistrationMember::create([
            'registration_id' => $registration->id,
            'name' => 'Jamaah Test 1',
            'nik' => '3201010101900001',
            'no_kk' => '3201010101900001',
            'relationship' => 'diri_sendiri',
            'document_status' => RegistrationMember::DOC_STATUS_DISETUJUI,
        ]);

        $departureTypes = DocumentType::departurePhase()->get();
        foreach ($departureTypes as $type) {
            JamaahDocument::create([
                'registration_member_id' => $member->id,
                'document_type_id' => $type->id,
                'file_path' => 'documents/test.pdf',
                'status' => JamaahDocument::STATUS_VALID,
            ]);
        }

        $service = app(BookingStatusService::class);
        $status = $service->evaluateStatus($registration);

        $this->assertEquals(Registration::STATUS_BERANGKAT, $status);
        $this->assertEquals(8, $registration->fresh()->step_number);
    }

    public function test_rejection_shows_reupload_form_and_resubmission_resets_status(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['role' => 'jamaah']);
        $admin = User::factory()->create(['role' => 'admin']);

        $package = Package::create([
            'name' => 'Paket Umrah Rejection Test',
            'price' => 35000000,
            'departure_date' => now()->addDays(60),
            'duration' => 12,
            'quota' => 45,
            'facilities' => 'Full Board Hotel Bintang 5',
            'status' => Package::STATUS_AKTIF,
        ]);

        $registration = Registration::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'status' => Registration::STATUS_MENUNGGU_KELENGKAPAN_KEBERANGKATAN,
        ]);

        $member = RegistrationMember::create([
            'registration_id' => $registration->id,
            'name' => 'Ahmad Jamaah',
            'nik' => '3201010101900001',
            'no_kk' => '3201010101900001',
            'relationship' => 'diri_sendiri',
            'document_status' => RegistrationMember::DOC_STATUS_DISETUJUI,
        ]);

        $visaType = DocumentType::where('code', DocumentType::CODE_VISA)->first();
        $this->assertNotNull($visaType);

        // 1. Initial upload (PDF)
        $oldFile = UploadedFile::fake()->create('old_visa.pdf', 500, 'application/pdf');
        $oldPath = $oldFile->storeAs('documents/departure/visa', 'old_visa.pdf', 'public');

        $doc = JamaahDocument::create([
            'registration_member_id' => $member->id,
            'document_type_id' => $visaType->id,
            'file_path' => $oldPath,
            'status' => JamaahDocument::STATUS_MENUNGGU_VERIFIKASI,
        ]);

        // 2. Admin rejects this document
        $this->actingAs($admin)
            ->post(route('admin.documents.verify', $doc), [
                'action' => 'reject',
                'rejection_reason' => 'File scan visa buram dan barcode tidak terbaca.',
            ]);

        $doc->refresh();
        $this->assertEquals(JamaahDocument::STATUS_DITOLAK, $doc->status);
        $this->assertEquals('File scan visa buram dan barcode tidak terbaca.', $doc->rejection_reason);

        // 3. Check Jamaah Portal View:
        // Rejection reason must be visible
        // Re-upload form must be visible (contains action pointing to members.departure-documents.upload)
        // Button text must say 'Unggah Ulang Dokumen'
        $response = $this->actingAs($user)
            ->get(route('jamaah.my-registration'));

        $response->assertStatus(200);
        $response->assertSee('File scan visa buram dan barcode tidak terbaca.');
        $response->assertSee('Formulir Unggah Ulang Dokumen:');
        $response->assertSee('Unggah Ulang Dokumen');
        $response->assertSee(route('jamaah.members.departure-documents.upload', [$member, $visaType]));

        // 4. Jamaah uploads replacement document (PDF)
        $newFile = UploadedFile::fake()->create('new_visa_clear.pdf', 600, 'application/pdf');
        $uploadResponse = $this->actingAs($user)
            ->post(route('jamaah.members.departure-documents.upload', [$member, $visaType]), [
                'file' => $newFile,
            ]);

        $uploadResponse->assertSessionHas('success');

        // Verify only 1 document record exists for this member & type (no duplicate row)
        $this->assertEquals(1, JamaahDocument::where('registration_member_id', $member->id)->where('document_type_id', $visaType->id)->count());

        $doc->refresh();
        $this->assertEquals(JamaahDocument::STATUS_MENUNGGU_VERIFIKASI, $doc->status);
        $this->assertNull($doc->rejection_reason, 'Rejection reason must be reset to null');
        $this->assertNull($doc->verified_by, 'Verified by must be reset to null');
        $this->assertNull($doc->verified_at, 'Verified at must be reset to null');
        $this->assertNotEquals($oldPath, $doc->file_path, 'File path must be updated to new file');

        // Verify old file was deleted from storage
        Storage::disk('public')->assertMissing($oldPath);
        // Verify new file exists in storage
        Storage::disk('public')->assertExists($doc->file_path);

        // 5. Check Jamaah Portal View after re-upload:
        // Old rejection reason should NO LONGER appear
        // Status should show 'Menunggu Verifikasi Admin'
        // Upload form should NO LONGER appear (in process)
        $viewAfter = $this->actingAs($user)
            ->get(route('jamaah.my-registration'));

        $viewAfter->assertStatus(200);
        $viewAfter->assertDontSee('File scan visa buram dan barcode tidak terbaca.');
        $viewAfter->assertSee('Menunggu Verifikasi Admin');
        $viewAfter->assertDontSee('Formulir Unggah Ulang Dokumen:');
    }

    public function test_valid_document_cannot_be_rejected_when_booking_status_is_not_berangkat(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'jamaah']);

        $package = Package::create([
            'name' => 'Paket Lock Test ' . uniqid(),
            'price' => 35000000,
            'departure_date' => now()->addDays(60),
            'duration' => 12,
            'quota' => 45,
            'facilities' => 'Full Board Hotel Bintang 5',
            'status' => Package::STATUS_AKTIF,
        ]);

        $registration = Registration::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'status' => Registration::STATUS_MENUNGGU_KELENGKAPAN_KEBERANGKATAN,
        ]);

        $member = RegistrationMember::create([
            'registration_id' => $registration->id,
            'name' => 'Jamaah Lock Test',
            'nik' => '3201010101900002',
            'no_kk' => '3201010101900002',
            'relationship' => 'diri_sendiri',
            'document_status' => RegistrationMember::DOC_STATUS_DISETUJUI,
        ]);

        $visaType = DocumentType::where('code', DocumentType::CODE_VISA)->first();

        $doc = JamaahDocument::create([
            'registration_member_id' => $member->id,
            'document_type_id' => $visaType->id,
            'file_path' => 'documents/valid_visa.pdf',
            'status' => JamaahDocument::STATUS_VALID,
            'verified_at' => now(),
            'verified_by' => $admin->id,
        ]);

        // Attempting to reject a VALID document when booking is NOT 'berangkat' must be aborted with 422
        $response = $this->actingAs($admin)
            ->post(route('admin.documents.verify', $doc), [
                'action' => 'reject',
                'rejection_reason' => 'Mencoba menolak dokumen yang sudah valid padahal belum berangkat',
            ]);

        $response->assertStatus(422);

        // Document status remains VALID
        $doc->refresh();
        $this->assertEquals(JamaahDocument::STATUS_VALID, $doc->status);
        $this->assertNull($doc->rejection_reason);
    }

    public function test_valid_document_can_be_rejected_when_booking_status_is_berangkat_as_emergency_correction(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'jamaah']);

        $package = Package::create([
            'name' => 'Paket Emergency Correction Test ' . uniqid(),
            'price' => 35000000,
            'departure_date' => now()->addDays(60),
            'duration' => 12,
            'quota' => 45,
            'facilities' => 'Full Board Hotel Bintang 5',
            'status' => Package::STATUS_AKTIF,
        ]);

        $registration = Registration::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'status' => Registration::STATUS_BERANGKAT,
        ]);

        $member = RegistrationMember::create([
            'registration_id' => $registration->id,
            'name' => 'Jamaah Correction Test',
            'nik' => '3201010101900003',
            'no_kk' => '3201010101900003',
            'relationship' => 'diri_sendiri',
            'document_status' => RegistrationMember::DOC_STATUS_DISETUJUI,
        ]);

        $visaType = DocumentType::where('code', DocumentType::CODE_VISA)->first();

        $doc = JamaahDocument::create([
            'registration_member_id' => $member->id,
            'document_type_id' => $visaType->id,
            'file_path' => 'documents/correction_visa.pdf',
            'status' => JamaahDocument::STATUS_VALID,
            'verified_at' => now(),
            'verified_by' => $admin->id,
        ]);

        // When booking is 'berangkat', emergency correction is allowed
        $response = $this->actingAs($admin)
            ->post(route('admin.documents.verify', $doc), [
                'action' => 'reject',
                'rejection_reason' => 'Koreksi darurat: Ditemukan nomor paspor pada visa tidak sesuai tiket.',
            ]);

        $response->assertSessionHas('success');

        // Document status updated to ditolak
        $doc->refresh();
        $this->assertEquals(JamaahDocument::STATUS_DITOLAK, $doc->status);
        $this->assertEquals('Koreksi darurat: Ditemukan nomor paspor pada visa tidak sesuai tiket.', $doc->rejection_reason);

        // Registration status automatically reverts to menunggu_kelengkapan_keberangkatan
        $registration->refresh();
        $this->assertEquals(Registration::STATUS_MENUNGGU_KELENGKAPAN_KEBERANGKATAN, $registration->status);
    }

    public function test_admin_view_shows_correction_button_only_when_booking_is_berangkat(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'jamaah']);

        $package = Package::create([
            'name' => 'Paket View Test ' . uniqid(),
            'price' => 35000000,
            'departure_date' => now()->addDays(60),
            'duration' => 12,
            'quota' => 45,
            'facilities' => 'Full Board Hotel Bintang 5',
            'status' => Package::STATUS_AKTIF,
        ]);

        $registration = Registration::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'status' => Registration::STATUS_MENUNGGU_KELENGKAPAN_KEBERANGKATAN,
        ]);

        $member = RegistrationMember::create([
            'registration_id' => $registration->id,
            'name' => 'Jamaah View Test',
            'nik' => '3201010101900004',
            'no_kk' => '3201010101900004',
            'relationship' => 'diri_sendiri',
            'document_status' => RegistrationMember::DOC_STATUS_DISETUJUI,
        ]);

        $visaType = DocumentType::where('code', DocumentType::CODE_VISA)->first();

        JamaahDocument::create([
            'registration_member_id' => $member->id,
            'document_type_id' => $visaType->id,
            'file_path' => 'documents/view_visa.pdf',
            'status' => JamaahDocument::STATUS_VALID,
            'verified_at' => now(),
            'verified_by' => $admin->id,
        ]);

        // 1. In MENUNGGU_KELENGKAPAN_KEBERANGKATAN: Button Batalkan/Tolak must NOT be visible for Valid doc
        $response1 = $this->actingAs($admin)
            ->get(route('admin.registrations.show', $registration));

        $response1->assertStatus(200);
        $response1->assertSee('Buka File PDF');
        $response1->assertDontSee('Batalkan Verifikasi (Koreksi)');
        $response1->assertDontSee('Batalkan / Tolak');

        // 2. In BERANGKAT: Button Batalkan Verifikasi (Koreksi) MUST be visible
        $registration->update(['status' => Registration::STATUS_BERANGKAT]);
        $response2 = $this->actingAs($admin)
            ->get(route('admin.registrations.show', $registration));

        $response2->assertStatus(200);
        $response2->assertSee('Batalkan Verifikasi (Koreksi)');

        // 3. In SELESAI: Button Batalkan Verifikasi (Koreksi) must NOT be visible
        $registration->update(['status' => Registration::STATUS_SELESAI]);
        $response3 = $this->actingAs($admin)
            ->get(route('admin.registrations.show', $registration));

        $response3->assertStatus(200);
        $response3->assertDontSee('Batalkan Verifikasi (Koreksi)');
        $response3->assertDontSee('Batalkan / Tolak');
    }
}
