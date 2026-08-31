<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Package;
use App\Models\PackageVariant;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PackageBillingAutomationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'phone_verified_at' => now(),
        ]);
    }

    private function createJamaahUser(): User
    {
        return User::factory()->create([
            'role' => 'jamaah',
            'phone_verified_at' => now(),
        ]);
    }

    private function createMemberPayload(string $name = 'Ahmad Jamaah', string $nik = '3201010101010001', string $rel = 'diri_sendiri'): array
    {
        return [
            'name' => $name,
            'birth_place' => 'Bandung',
            'birth_date' => '1990-01-01',
            'gender' => 'laki-laki',
            'address' => 'Jl. Merdeka No. 1',
            'nik' => $nik,
            'no_kk' => '3273010101900000',
            'relationship' => $rel,
            'ktp_file' => UploadedFile::fake()->image('ktp.jpg', 600, 400),
            'kk_file' => UploadedFile::fake()->image('kk.jpg', 600, 400),
        ];
    }

    public function test_1_single_package_registration_sets_exact_price_as_invoice_total(): void
    {
        $packageA = Package::create([
            'name' => 'Paket A Regular',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 9,
            'status' => 'aktif',
            'price' => 25000000,
            'facilities' => '-',
            'quota' => 30,
        ]);

        $user = $this->createJamaahUser();

        $response = $this->actingAs($user)->post(route('jamaah.registration.store'), [
            'package_id' => $packageA->id,
            'room_type' => 'quad',
            'members' => [$this->createMemberPayload()],
        ]);

        $response->assertRedirect(route('jamaah.my-registration'));

        $registration = Registration::where('user_id', $user->id)->first();
        $this->assertNotNull($registration);
        $this->assertNotNull($registration->invoice);

        $this->assertEquals(25000000, (float) $registration->invoice->total_price);
        $this->assertEquals(0, (float) $registration->invoice->total_paid);
        $this->assertEquals(25000000, (float) $registration->invoice->remaining_balance);
    }

    public function test_2_package_b_registration_sets_exact_price_as_invoice_total(): void
    {
        $packageB = Package::create([
            'name' => 'Paket B Plus',
            'departure_date' => now()->addMonths(3)->format('Y-m-d'),
            'duration' => 12,
            'status' => 'aktif',
            'price' => 30000000,
            'facilities' => '-',
            'quota' => 30,
        ]);

        $user = $this->createJamaahUser();

        $response = $this->actingAs($user)->post(route('jamaah.registration.store'), [
            'package_id' => $packageB->id,
            'room_type' => 'quad',
            'members' => [$this->createMemberPayload('Budi Jamaah', '3201010101010002')],
        ]);

        $response->assertRedirect(route('jamaah.my-registration'));

        $registration = Registration::where('user_id', $user->id)->first();
        $this->assertNotNull($registration);
        $this->assertEquals(30000000, (float) $registration->invoice->total_price);
    }

    public function test_3_variants_pricing_sets_exact_price_for_vip_bisnis_and_ekonomi(): void
    {
        $package = Package::create([
            'name' => 'Umroh Ramadhan',
            'departure_date' => now()->addMonths(4)->format('Y-m-d'),
            'duration' => 12,
            'status' => 'aktif',
            'price' => 0,
            'facilities' => '-',
            'quota' => 50,
        ]);

        $varVip = $package->variants()->create(['name' => 'VIP', 'quota' => 10, 'status' => 'aktif']);
        $varVip->prices()->create(['room_type' => 'quad', 'normal_price' => 40000000, 'is_active' => true]);

        $varBisnis = $package->variants()->create(['name' => 'Bisnis', 'quota' => 20, 'status' => 'aktif']);
        $varBisnis->prices()->create(['room_type' => 'quad', 'normal_price' => 32000000, 'is_active' => true]);

        $varEkonomi = $package->variants()->create(['name' => 'Ekonomi', 'quota' => 20, 'status' => 'aktif']);
        $varEkonomi->prices()->create(['room_type' => 'quad', 'normal_price' => 27000000, 'is_active' => true]);

        // Daftar VIP
        $user1 = $this->createJamaahUser();
        $this->actingAs($user1)->post(route('jamaah.registration.store'), [
            'package_id' => $package->id,
            'package_variant_id' => $varVip->id,
            'room_type' => 'quad',
            'members' => [$this->createMemberPayload('User VIP', '3201010101010011')],
        ]);
        $reg1 = Registration::where('user_id', $user1->id)->first();
        $this->assertNotNull($reg1);
        $this->assertEquals(40000000, (float) $reg1->invoice->total_price);

        // Daftar Bisnis
        $user2 = $this->createJamaahUser();
        $this->actingAs($user2)->post(route('jamaah.registration.store'), [
            'package_id' => $package->id,
            'package_variant_id' => $varBisnis->id,
            'room_type' => 'quad',
            'members' => [$this->createMemberPayload('User Bisnis', '3201010101010012')],
        ]);
        $reg2 = Registration::where('user_id', $user2->id)->first();
        $this->assertNotNull($reg2);
        $this->assertEquals(32000000, (float) $reg2->invoice->total_price);

        // Daftar Ekonomi
        $user3 = $this->createJamaahUser();
        $this->actingAs($user3)->post(route('jamaah.registration.store'), [
            'package_id' => $package->id,
            'package_variant_id' => $varEkonomi->id,
            'room_type' => 'quad',
            'members' => [$this->createMemberPayload('User Ekonomi', '3201010101010013')],
        ]);
        $reg3 = Registration::where('user_id', $user3->id)->first();
        $this->assertNotNull($reg3);
        $this->assertEquals(27000000, (float) $reg3->invoice->total_price);
    }

    public function test_4_payment_verification_accurately_updates_total_paid_and_remaining_balance(): void
    {
        $package = Package::create([
            'name' => 'Umroh Hemat',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 9,
            'status' => 'aktif',
            'price' => 30000000,
            'facilities' => '-',
            'quota' => 20,
        ]);

        $user = $this->createJamaahUser();
        $this->actingAs($user)->post(route('jamaah.registration.store'), [
            'package_id' => $package->id,
            'room_type' => 'quad',
            'members' => [$this->createMemberPayload('Jamaah DP', '3201010101010020')],
        ]);

        $registration = Registration::where('user_id', $user->id)->first();
        $invoice = $registration->invoice;
        $this->assertEquals(30000000, (float) $invoice->total_price);

        // Submit DP 10.000.000
        $payment = Payment::create([
            'registration_id' => $registration->id,
            'type' => Payment::TYPE_DP,
            'amount' => 10000000,
            'proof_file' => 'payments/dp/proof.jpg',
            'status' => Payment::STATUS_MENUNGGU_VERIFIKASI,
        ]);

        // Admin approve payment
        $response = $this->actingAs($this->admin)->post(route('admin.payments.verify', $payment), [
            'action' => 'approve',
        ]);
        $response->assertRedirect();

        $invoice->refresh();
        $this->assertEquals(10000000, (float) $invoice->total_paid);
        $this->assertEquals(20000000, (float) $invoice->remaining_balance);
        $this->assertEquals(Registration::STATUS_JAMAAH, $registration->fresh()->status);
    }

    public function test_5_updating_package_price_later_does_not_change_existing_registration_invoices(): void
    {
        $package = Package::create([
            'name' => 'Umroh Promo Awal Tahun',
            'departure_date' => now()->addMonths(3)->format('Y-m-d'),
            'duration' => 9,
            'status' => 'aktif',
            'price' => 30000000,
            'facilities' => '-',
            'quota' => 20,
        ]);

        // Jamaah 1 daftar saat harga 30.000.000
        $user1 = $this->createJamaahUser();
        $this->actingAs($user1)->post(route('jamaah.registration.store'), [
            'package_id' => $package->id,
            'room_type' => 'quad',
            'members' => [$this->createMemberPayload('Jamaah 1', '3201010101010031')],
        ]);
        $reg1 = Registration::where('user_id', $user1->id)->first();
        $this->assertEquals(30000000, (float) $reg1->invoice->total_price);

        // Admin mengubah harga paket menjadi 35.000.000
        $package->update(['price' => 35000000]);

        // Jamaah 1 invoice TETAP 30.000.000
        $reg1->invoice->refresh();
        $this->assertEquals(30000000, (float) $reg1->invoice->total_price);

        // Jamaah 2 mendaftar setelah harga berubah -> mendapat tagihan 35.000.000
        $user2 = $this->createJamaahUser();
        $this->actingAs($user2)->post(route('jamaah.registration.store'), [
            'package_id' => $package->id,
            'room_type' => 'quad',
            'members' => [$this->createMemberPayload('Jamaah 2', '3201010101010032')],
        ]);
        $reg2 = Registration::where('user_id', $user2->id)->first();
        $this->assertEquals(35000000, (float) $reg2->invoice->total_price);
    }

    public function test_6_frontend_price_manipulation_attempt_is_ignored_by_backend(): void
    {
        $package = Package::create([
            'name' => 'Paket Umroh Bintang 5',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 9,
            'status' => 'aktif',
            'price' => 40000000,
            'facilities' => '-',
            'quota' => 20,
        ]);

        $user = $this->createJamaahUser();

        // User mencoba menyisipkan field manipulasi harga palsu di request POST
        $this->actingAs($user)->post(route('jamaah.registration.store'), [
            'package_id' => $package->id,
            'room_type' => 'quad',
            'price' => 1000000,          // manipulasi harga palsu
            'total_price' => 500,        // manipulasi harga palsu
            'amount' => 100,             // manipulasi harga palsu
            'members' => [$this->createMemberPayload('Hacker Fake Price', '3201010101010040')],
        ]);

        $reg = Registration::where('user_id', $user->id)->first();
        $this->assertNotNull($reg);
        // Backend tetap menggunakan harga resmi database yaitu Rp 40.000.000
        $this->assertEquals(40000000, (float) $reg->invoice->total_price);
    }

    public function test_7_multi_member_registration_calculates_total_price_per_pax(): void
    {
        $package = Package::create([
            'name' => 'Umroh Keluarga',
            'departure_date' => now()->addMonths(2)->format('Y-m-d'),
            'duration' => 9,
            'status' => 'aktif',
            'price' => 32000000,
            'facilities' => '-',
            'quota' => 20,
        ]);

        $user = $this->createJamaahUser();

        // Daftar 2 pax anggota keluarga (diri_sendiri dan orang_tua)
        $member1 = $this->createMemberPayload('Ayah', '3201010101010051', 'diri_sendiri');
        $member2 = $this->createMemberPayload('Ibu', '3201010101010052', 'orang_tua');

        $this->actingAs($user)->post(route('jamaah.registration.store'), [
            'package_id' => $package->id,
            'room_type' => 'quad',
            'members' => [$member1, $member2],
        ]);

        $reg = Registration::where('user_id', $user->id)->first();
        $this->assertNotNull($reg);
        $this->assertEquals(2, $reg->members->count());
        // 2 pax * 32.000.000 = 64.000.000
        $this->assertEquals(64000000, (float) $reg->invoice->total_price);
        $this->assertEquals(64000000, (float) $reg->invoice->remaining_balance);
    }
}
