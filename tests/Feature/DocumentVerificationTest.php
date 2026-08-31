<?php

namespace Tests\Feature;

use App\Models\DocumentVerificationHistory;
use App\Models\Invoice;
use App\Models\Package;
use App\Models\Registration;
use App\Models\RegistrationMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $jamaah1;
    protected User $jamaah2;
    protected Package $package;
    protected Registration $registration1;
    protected RegistrationMember $member1;
    protected RegistrationMember $member2;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        // 1. Setup Admin User
        $this->admin = User::factory()->create([
            'name' => 'Admin Verifikator',
            'email' => 'admin@zeintour.com',
            'role' => 'admin',
            'password' => Hash::make('admin123'),
            'phone_verified_at' => now(),
        ]);

        // 2. Setup Jamaah 1 (Owner of registration)
        $this->jamaah1 = User::factory()->create([
            'name' => 'Jamaah Satu',
            'email' => 'jamaah1@zeintour.test',
            'phone' => '6281234567890',
            'role' => 'jamaah',
            'password' => Hash::make('password123'),
            'phone_verified_at' => now(),
        ]);

        // 3. Setup Jamaah 2 (Other user)
        $this->jamaah2 = User::factory()->create([
            'name' => 'Jamaah Dua',
            'email' => 'jamaah2@zeintour.test',
            'phone' => '6289876543210',
            'role' => 'jamaah',
            'password' => Hash::make('password123'),
            'phone_verified_at' => now(),
        ]);

        // 4. Setup Package
        $this->package = Package::create([
            'name' => 'Paket Umrah Reguler 12 Hari',
            'slug' => 'paket-umrah-reguler-12-hari',
            'price' => 30000000,
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 12,
            'facilities' => 'Hotel *4, Tiket PP, Visa, Bus AC, Makan 3x',
            'quota' => 45,
            'status' => 'aktif',
        ]);

        // 5. Setup Registration for Jamaah 1 with 2 members
        $this->registration1 = Registration::create([
            'user_id' => $this->jamaah1->id,
            'package_id' => $this->package->id,
            'status' => Registration::STATUS_MENUNGGU_VERIFIKASI_DOKUMEN,
        ]);

        Invoice::create([
            'registration_id' => $this->registration1->id,
            'total_price' => 60000000,
            'total_paid' => 0,
            'remaining_balance' => 60000000,
            'due_date' => now()->addDays(7),
        ]);

        $this->member1 = RegistrationMember::create([
            'registration_id' => $this->registration1->id,
            'name' => 'Jamaah Satu',
            'nik' => '3273010101900001',
            'ktp_file' => 'documents/ktp/ktp1.jpg',
            'no_kk' => '3273010101900000',
            'kk_file' => 'documents/kk/kk1.jpg',
            'passport_file' => 'documents/passport/passport1.jpg',
            'relationship' => 'diri_sendiri',
            'document_status' => RegistrationMember::DOC_STATUS_MENUNGGU_VERIFIKASI,
        ]);

        $this->member2 = RegistrationMember::create([
            'registration_id' => $this->registration1->id,
            'name' => 'Istri Jamaah Satu',
            'nik' => '3273010101920002',
            'ktp_file' => 'documents/ktp/ktp2.jpg',
            'no_kk' => '3273010101900000',
            'kk_file' => 'documents/kk/kk1.jpg',
            'passport_file' => 'documents/passport/passport2.jpg',
            'marriage_book_file' => 'documents/marriage/marriage1.jpg',
            'relationship' => 'istri',
            'document_status' => RegistrationMember::DOC_STATUS_MENUNGGU_VERIFIKASI,
        ]);
    }

    protected function createFakeImage(string $filename = 'test.jpg'): UploadedFile
    {
        return UploadedFile::fake()->image($filename, 800, 600);
    }

    /**
     * TEST 1: PENDING → VERIFY => VERIFIED
     * Dokumen status berubah jadi disetujui, verified_at & verified_by tercatat,
     * history tercatat, dan jika semua disetujui registration berubah ke menunggu_pembayaran_dp.
     */
    public function test_test1_pending_to_verify_sets_verified_and_records_history(): void
    {
        $this->actingAs($this->admin);

        // Verify member 1
        $response = $this->post(route('admin.members.verify', $this->member1), [
            'action' => 'approve',
        ]);

        $response->assertSessionHas('success');
        $this->member1->refresh();
        $this->assertEquals(RegistrationMember::DOC_STATUS_DISETUJUI, $this->member1->document_status);
        $this->assertNotNull($this->member1->verified_at);
        $this->assertEquals($this->admin->id, $this->member1->verified_by);
        $this->assertNull($this->member1->rejection_reason);

        // History audit
        $this->assertDatabaseHas('document_verification_histories', [
            'registration_member_id' => $this->member1->id,
            'status' => RegistrationMember::DOC_STATUS_DISETUJUI,
            'action_by' => $this->admin->id,
        ]);

        // Registration still pending because member 2 is still unapproved
        $this->registration1->refresh();
        $this->assertEquals(Registration::STATUS_MENUNGGU_VERIFIKASI_DOKUMEN, $this->registration1->status);

        // Now verify member 2
        $this->post(route('admin.members.verify', $this->member2), [
            'action' => 'approve',
        ]);

        $this->registration1->refresh();
        $this->assertEquals(Registration::STATUS_MENUNGGU_PEMBAYARAN_DP, $this->registration1->status);
    }

    /**
     * TEST 2: PENDING → REJECT => REJECTED with rejection_reason
     */
    public function test_test2_pending_to_reject_sets_rejected_and_stores_reason(): void
    {
        $this->actingAs($this->admin);

        $reason = 'Foto KTP buram, nomor NIK tidak terbaca jelas. Mohon upload ulang.';

        $response = $this->post(route('admin.members.verify', $this->member1), [
            'action' => 'reject',
            'rejection_reason' => $reason,
        ]);

        $response->assertSessionHas('success');
        $this->member1->refresh();
        $this->assertEquals(RegistrationMember::DOC_STATUS_DITOLAK, $this->member1->document_status);
        $this->assertEquals($reason, $this->member1->rejection_reason);
        $this->assertNotNull($this->member1->rejected_at);
        $this->assertEquals($this->admin->id, $this->member1->rejected_by);

        // History audit
        $this->assertDatabaseHas('document_verification_histories', [
            'registration_member_id' => $this->member1->id,
            'status' => RegistrationMember::DOC_STATUS_DITOLAK,
            'reason' => $reason,
            'action_by' => $this->admin->id,
        ]);
    }

    /**
     * TEST 3: Admin tries VERIFIED → REJECT => DITOLAK OLEH BACKEND (status remains VERIFIED)
     */
    public function test_test3_admin_cannot_reject_verified_document(): void
    {
        $this->actingAs($this->admin);

        // First approve member 1
        $this->member1->update([
            'document_status' => RegistrationMember::DOC_STATUS_DISETUJUI,
            'verified_at' => now(),
            'verified_by' => $this->admin->id,
        ]);

        // Now attempt to reject the already verified member
        $response = $this->post(route('admin.members.verify', $this->member1), [
            'action' => 'reject',
            'rejection_reason' => 'Ingin menolak dokumen yang sudah disetujui',
        ]);

        $response->assertSessionHas('error');
        $this->member1->refresh();
        $this->assertEquals(RegistrationMember::DOC_STATUS_DISETUJUI, $this->member1->document_status);
        $this->assertNull($this->member1->rejection_reason);
    }

    /**
     * TEST 4: Admin tries PENDING → REJECT with empty reason => VALIDATION ERROR, status remains PENDING
     */
    public function test_test4_reject_without_reason_fails_validation_and_status_remains_pending(): void
    {
        $this->actingAs($this->admin);

        // Case A: empty string
        $response = $this->post(route('admin.members.verify', $this->member1), [
            'action' => 'reject',
            'rejection_reason' => '',
        ]);

        $response->assertSessionHasErrors(['rejection_reason']);
        $this->member1->refresh();
        $this->assertEquals(RegistrationMember::DOC_STATUS_MENUNGGU_VERIFIKASI, $this->member1->document_status);

        // Case B: whitespace only
        $response2 = $this->post(route('admin.members.verify', $this->member1), [
            'action' => 'reject',
            'rejection_reason' => '     ',
        ]);

        $response2->assertSessionHasErrors(['rejection_reason']);
        $this->member1->refresh();
        $this->assertEquals(RegistrationMember::DOC_STATUS_MENUNGGU_VERIFIKASI, $this->member1->document_status);

        // Case C: too short (< 5 chars)
        $response3 = $this->post(route('admin.members.verify', $this->member1), [
            'action' => 'reject',
            'rejection_reason' => 'abc',
        ]);

        $response3->assertSessionHasErrors(['rejection_reason']);
        $this->member1->refresh();
        $this->assertEquals(RegistrationMember::DOC_STATUS_MENUNGGU_VERIFIKASI, $this->member1->document_status);
    }

    /**
     * TEST 5: Jamaah views REJECTED document => status = REJECTED, rejection reason & full resubmit form visible
     */
    public function test_test5_jamaah_sees_rejected_status_and_rejection_reason_on_status_page(): void
    {
        $reason = 'Foto KTP tidak jelas dan KK tidak sesuai.';
        $this->member1->update([
            'document_status' => RegistrationMember::DOC_STATUS_DITOLAK,
            'rejection_reason' => $reason,
            'rejected_at' => now(),
            'rejected_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->jamaah1)->get(route('jamaah.my-registration'));

        $response->assertOk();
        $response->assertSee('Mode Revisi Total');
        $response->assertSee($reason);
        $response->assertSee('Formulir Pengajuan Ulang', false);
        $response->assertSee('Kirim Ulang Seluruh Data', false);
    }

    /**
     * TEST 6: Jamaah resubmits ALL required identity data & documents on REJECTED => REJECTED → PENDING
     */
    public function test_test6_jamaah_resubmit_all_documents_changes_status_to_pending(): void
    {
        $this->member1->update([
            'document_status' => RegistrationMember::DOC_STATUS_DITOLAK,
            'rejection_reason' => 'KTP dan KK buram',
            'rejected_at' => now(),
            'rejected_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->jamaah1)->post(route('jamaah.members.documents.update', $this->member1), [
            'name' => 'Ahmad Fauzi Revisi',
            'birth_place' => 'Bandung',
            'birth_date' => '1990-01-01',
            'gender' => 'laki-laki',
            'nik' => '3273010101900001',
            'address' => 'Jl. Asia Afrika No. 1, Bandung',
            'no_kk' => '3273010101900000',
            'relationship' => 'diri_sendiri',
            'ktp_file' => $this->createFakeImage('new_ktp.jpg'),
            'kk_file' => $this->createFakeImage('new_kk.jpg'),
            'passport_file' => $this->createFakeImage('new_passport.jpg'),
        ]);

        $response->assertSessionHas('success');
        $this->member1->refresh();
        $this->assertEquals(RegistrationMember::DOC_STATUS_MENUNGGU_VERIFIKASI, $this->member1->document_status);
        $this->assertEquals('Ahmad Fauzi Revisi', $this->member1->name);
        $this->assertEquals('3273010101900001', $this->member1->nik);
        $this->assertEquals('3273010101900000', $this->member1->no_kk);
        $this->assertNull($this->member1->rejection_reason);

        // Check history was recorded
        $this->assertDatabaseHas('document_verification_histories', [
            'registration_member_id' => $this->member1->id,
            'status' => RegistrationMember::DOC_STATUS_MENUNGGU_VERIFIKASI,
            'action_by' => $this->jamaah1->id,
        ]);
    }

    /**
     * TEST 7: Jamaah partial data / document submission is strictly REJECTED by backend
     */
    public function test_test7_jamaah_partial_resubmit_is_rejected(): void
    {
        $this->member1->update([
            'document_status' => RegistrationMember::DOC_STATUS_DITOLAK,
            'rejection_reason' => 'Semua data dan dokumen perlu diisi ulang',
        ]);

        // Attempt to upload without nik and kk and without kk_file
        $response = $this->actingAs($this->jamaah1)->post(route('jamaah.members.documents.update', $this->member1), [
            'name' => 'Ahmad Fauzi',
            'relationship' => 'diri_sendiri',
            'ktp_file' => $this->createFakeImage('new_ktp.jpg'),
            // nik missing
            // no_kk missing
            // kk_file missing
        ]);

        $response->assertSessionHasErrors(['nik', 'no_kk', 'kk_file']);
        $this->member1->refresh();
        $this->assertEquals(RegistrationMember::DOC_STATUS_DITOLAK, $this->member1->document_status);
    }

    /**
     * TEST 8: Admin cannot directly approve REJECTED document without Jamaah full resubmit
     */
    public function test_test8_admin_cannot_directly_approve_rejected_document(): void
    {
        $this->actingAs($this->admin);

        $this->member1->update([
            'document_status' => RegistrationMember::DOC_STATUS_DITOLAK,
            'rejection_reason' => 'Foto KTP tidak terbaca',
        ]);

        $response = $this->post(route('admin.members.verify', $this->member1), [
            'action' => 'approve',
        ]);

        $response->assertSessionHas('error');
        $this->member1->refresh();
        $this->assertEquals(RegistrationMember::DOC_STATUS_DITOLAK, $this->member1->document_status);
    }

    /**
     * TEST 9: Jamaah tries modifying VERIFIED document => DITOLAK (backend protection)
     */
    public function test_test9_jamaah_cannot_modify_verified_document(): void
    {
        $this->member1->update([
            'document_status' => RegistrationMember::DOC_STATUS_DISETUJUI,
            'verified_at' => now(),
            'verified_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->jamaah1)->post(route('jamaah.members.documents.update', $this->member1), [
            'name' => 'Hacker Name',
            'nik' => '3273010101900001',
            'no_kk' => '3273010101900000',
            'relationship' => 'diri_sendiri',
            'ktp_file' => $this->createFakeImage('tampered_ktp.jpg'),
            'kk_file' => $this->createFakeImage('tampered_kk.jpg'),
            'passport_file' => $this->createFakeImage('tampered_passport.jpg'),
        ]);

        $response->assertSessionHas('error');
        $this->member1->refresh();
        $this->assertEquals(RegistrationMember::DOC_STATUS_DISETUJUI, $this->member1->document_status);
    }

    /**
     * TEST 10: Jamaah tries modifying other jamaah's document => 403 Forbidden
     */
    public function test_test10_jamaah_cannot_modify_another_jamaahs_document(): void
    {
        // Jamaah 2 tries to update member 1 belonging to Jamaah 1
        $response = $this->actingAs($this->jamaah2)->post(route('jamaah.members.documents.update', $this->member1), [
            'name' => 'Hacker Jamaah',
            'nik' => '3273010101900001',
            'no_kk' => '3273010101900000',
            'relationship' => 'diri_sendiri',
            'ktp_file' => $this->createFakeImage('hacker_ktp.jpg'),
            'kk_file' => $this->createFakeImage('hacker_kk.jpg'),
            'passport_file' => $this->createFakeImage('hacker_passport.jpg'),
        ]);
        $response->assertStatus(403);
    }

    /**
     * TEST 11: Admin performs VERIFY twice => Idempotent, returns warning and does not corrupt status
     */
    public function test_test11_admin_verify_twice_is_idempotent(): void
    {
        $this->actingAs($this->admin);

        // First verification
        $this->post(route('admin.members.verify', $this->member1), [
            'action' => 'approve',
        ]);

        $historyCountFirst = DocumentVerificationHistory::where('registration_member_id', $this->member1->id)->count();

        // Second verification
        $response = $this->post(route('admin.members.verify', $this->member1), [
            'action' => 'approve',
        ]);

        $response->assertSessionHas('warning');
        $this->member1->refresh();
        $this->assertEquals(RegistrationMember::DOC_STATUS_DISETUJUI, $this->member1->document_status);

        // History should not have duplicated approved entry
        $historyCountSecond = DocumentVerificationHistory::where('registration_member_id', $this->member1->id)->count();
        $this->assertEquals($historyCountFirst, $historyCountSecond);
    }

    /**
     * TEST 12: Direct API/JSON request to APPROVE REJECTED document returns 422
     */
    public function test_test12_direct_json_approve_on_rejected_document_returns_422(): void
    {
        $this->actingAs($this->admin);

        $this->member1->update([
            'document_status' => RegistrationMember::DOC_STATUS_DITOLAK,
            'rejection_reason' => 'KTP buram',
        ]);

        $response = $this->postJson(route('admin.members.verify', $this->member1), [
            'action' => 'approve',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
        $this->member1->refresh();
        $this->assertEquals(RegistrationMember::DOC_STATUS_DITOLAK, $this->member1->document_status);
    }

    /**
     * TEST 13: Direct API/JSON request to REJECT VERIFIED document returns 422
     */
    public function test_test13_direct_json_reject_on_verified_document_returns_422(): void
    {
        $this->actingAs($this->admin);

        $this->member1->update([
            'document_status' => RegistrationMember::DOC_STATUS_DISETUJUI,
            'verified_at' => now(),
            'verified_by' => $this->admin->id,
        ]);

        $response = $this->postJson(route('admin.members.verify', $this->member1), [
            'action' => 'reject',
            'rejection_reason' => 'Mencoba menolak dokumen via API',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
        $this->member1->refresh();
        $this->assertEquals(RegistrationMember::DOC_STATUS_DISETUJUI, $this->member1->document_status);
    }

    /**
     * TEST 14: Resubmission with non-digit or non-16-digit NIK is rejected by backend
     */
    public function test_test14_resubmission_with_invalid_nik_is_rejected(): void
    {
        $this->member1->update([
            'document_status' => RegistrationMember::DOC_STATUS_DITOLAK,
            'rejection_reason' => 'NIK tidak sesuai KTP',
        ]);

        $response = $this->actingAs($this->jamaah1)->post(route('jamaah.members.documents.update', $this->member1), [
            'name' => 'Ahmad Fauzi',
            'nik' => '327301010190000A', // Invalid letters
            'no_kk' => '3273010101900000',
            'relationship' => 'diri_sendiri',
            'ktp_file' => $this->createFakeImage('new_ktp.jpg'),
            'kk_file' => $this->createFakeImage('new_kk.jpg'),
            'passport_file' => $this->createFakeImage('new_passport.jpg'),
        ]);

        $response->assertSessionHasErrors(['nik']);
        $this->member1->refresh();
        $this->assertEquals(RegistrationMember::DOC_STATUS_DITOLAK, $this->member1->document_status);
    }

    /**
     * TEST 15: Resubmission with non-digit or non-16-digit No KK is rejected by backend
     */
    public function test_test15_resubmission_with_invalid_no_kk_is_rejected(): void
    {
        $this->member1->update([
            'document_status' => RegistrationMember::DOC_STATUS_DITOLAK,
            'rejection_reason' => 'No KK tidak sesuai',
        ]);

        $response = $this->actingAs($this->jamaah1)->post(route('jamaah.members.documents.update', $this->member1), [
            'name' => 'Ahmad Fauzi',
            'nik' => '3273010101900001',
            'no_kk' => '3273-01010190-00', // Invalid symbols
            'relationship' => 'diri_sendiri',
            'ktp_file' => $this->createFakeImage('new_ktp.jpg'),
            'kk_file' => $this->createFakeImage('new_kk.jpg'),
            'passport_file' => $this->createFakeImage('new_passport.jpg'),
        ]);

        $response->assertSessionHasErrors(['no_kk']);
        $this->member1->refresh();
        $this->assertEquals(RegistrationMember::DOC_STATUS_DITOLAK, $this->member1->document_status);
    }

    /**
     * TEST 16: Complete total revision updates all identity fields and resets status to pending
     */
    public function test_test16_total_revision_updates_all_identity_fields_and_sets_pending(): void
    {
        $this->member1->update([
            'document_status' => RegistrationMember::DOC_STATUS_DITOLAK,
            'rejection_reason' => 'Data identitas dan dokumen salah total',
        ]);

        $response = $this->actingAs($this->jamaah1)->post(route('jamaah.members.documents.update', $this->member1), [
            'name' => 'Ahmad Fauzi Baru',
            'nik' => '3273012345678901',
            'no_kk' => '3273012345678902',
            'no_passport' => 'B9988776',
            'relationship' => 'diri_sendiri',
            'birth_place' => 'Bandung',
            'birth_date' => '1992-05-15',
            'gender' => 'laki-laki',
            'address' => 'Jl. Merdeka No. 45, Bandung',
            'phone' => '081234567890',
            'ktp_file' => $this->createFakeImage('new_ktp.jpg'),
            'kk_file' => $this->createFakeImage('new_kk.jpg'),
            'passport_file' => $this->createFakeImage('new_passport.jpg'),
        ]);

        $response->assertSessionHas('success');
        $this->member1->refresh();
        $this->assertEquals(RegistrationMember::DOC_STATUS_MENUNGGU_VERIFIKASI, $this->member1->document_status);
        $this->assertEquals('Ahmad Fauzi Baru', $this->member1->name);
        $this->assertEquals('3273012345678901', $this->member1->nik);
        $this->assertEquals('3273012345678902', $this->member1->no_kk);
        $this->assertEquals('B9988776', $this->member1->no_passport);
        $this->assertNull($this->member1->rejection_reason);

        $this->jamaah1->refresh();
        $this->assertEquals('Ahmad Fauzi Baru', $this->jamaah1->name);
        $this->assertEquals('Bandung', $this->jamaah1->birth_place);
    }

    /**
     * TEST 17: Resubmission without passport succeeds and leaves passport as null or old value
     */
    public function test_test17_resubmission_succeeds_without_passport(): void
    {
        $this->member1->update([
            'document_status' => RegistrationMember::DOC_STATUS_DITOLAK,
            'passport_file' => null,
            'no_passport' => null,
            'rejection_reason' => 'Perbaiki berkas KTP dan KK',
        ]);

        $response = $this->actingAs($this->jamaah1)->post(route('jamaah.members.documents.update', $this->member1), [
            'name' => 'Ahmad Fauzi',
            'birth_place' => 'Bandung',
            'birth_date' => '1990-01-01',
            'gender' => 'laki-laki',
            'nik' => '3273012345678901',
            'address' => 'Jl. Asia Afrika No. 1, Bandung',
            'no_kk' => '3273012345678902',
            'relationship' => 'diri_sendiri',
            'ktp_file' => $this->createFakeImage('new_ktp.jpg'),
            'kk_file' => $this->createFakeImage('new_kk.jpg'),
            // No passport_file provided
        ]);

        $response->assertSessionHas('success');
        $this->member1->refresh();
        $this->assertEquals(RegistrationMember::DOC_STATUS_MENUNGGU_VERIFIKASI, $this->member1->document_status);
        $this->assertNull($this->member1->passport_file);
        $this->assertNull($this->member1->no_passport);
    }

    /**
     * TEST 18: Initial registration succeeds without passport
     */
    public function test_test18_registration_succeeds_without_passport(): void
    {
        $user = \App\Models\User::factory()->create([
            'role' => 'jamaah',
            'phone_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->post(route('jamaah.registration.store'), [
            'package_id' => $this->package->id,
            'members' => [
                [
                    'name' => 'Jamaah Tanpa Paspor',
                    'birth_place' => 'Jakarta',
                    'birth_date' => '1995-08-17',
                    'gender' => 'perempuan',
                    'nik' => '3201234567890001',
                    'address' => 'Jl. Sudirman No. 10, Jakarta',
                    'ktp_file' => $this->createFakeImage('ktp_user.jpg'),
                    'no_kk' => '3201234567890002',
                    'kk_file' => $this->createFakeImage('kk_user.jpg'),
                    'relationship' => 'diri_sendiri',
                    // No passport_file or no_passport
                ],
            ],
        ]);

        $response->assertRedirect(route('jamaah.my-registration'));
        $reg = \App\Models\Registration::where('user_id', $user->id)->first();
        $this->assertNotNull($reg);
        $member = $reg->members()->first();
        $this->assertNotNull($member);
        $this->assertNull($member->passport_file);
        $this->assertNull($member->no_passport);
        $this->assertNotNull($member->ktp_file);
        $this->assertNotNull($member->kk_file);
        $this->assertEquals('Jakarta', $member->birth_place);
        $this->assertEquals('1995-08-17', $member->birth_date->format('Y-m-d'));
        $this->assertEquals('perempuan', $member->gender);
        $this->assertEquals('Perempuan', $member->gender_label);
        $this->assertEquals('Jl. Sudirman No. 10, Jakarta', $member->address);
    }
}
