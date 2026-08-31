<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\RegistrationMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class NikKkValidationTest extends TestCase
{
    use RefreshDatabase;

    protected User $jamaah;
    protected Package $package;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jamaah = User::factory()->create([
            'role' => 'jamaah',
            'phone_verified_at' => now(),
        ]);

        $this->package = Package::create([
            'name' => 'Paket Umrah Reguler 1447H',
            'slug' => 'paket-umrah-reguler-1447h',
            'price' => 30000000,
            'departure_date' => now()->addDays(40),
            'duration' => 9,
            'quota' => 30,
            'facilities' => "Hotel Makkah *5\nHotel Madinah *4\nTiket PP",
            'status' => 'aktif',
        ]);
    }

    protected function createFakeImage(string $filename = 'test.jpg'): UploadedFile
    {
        return UploadedFile::fake()->image($filename, 600, 400);
    }

    protected function validMemberPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Ahmad Fauzi',
            'birth_place' => 'BANDUNG',
            'birth_date' => '1990-05-15',
            'gender' => 'laki-laki',
            'relationship' => 'diri_sendiri',
            'nik' => '3201234567890001',
            'address' => 'Jl. Merdeka No. 45, RT 01/RW 02, Bandung, Jawa Barat',
            'no_kk' => '3201234567890002',
            'ktp_file' => $this->createFakeImage('ktp.jpg'),
            'kk_file' => $this->createFakeImage('kk.jpg'),
            'passport_file' => $this->createFakeImage('passport.jpg'),
        ], $overrides);
    }

    /**
     * Test 1: NIK & No. KK valid (16 digit angka 0–9) dan identitas KTP lengkap diterima.
     */
    public function test_valid_16_digits_nik_and_kk_are_accepted(): void
    {
        $response = $this->actingAs($this->jamaah)
            ->post(route('jamaah.registration.store'), [
                'package_id' => $this->package->id,
                'members' => [
                    $this->validMemberPayload()
                ],
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('jamaah.my-registration'));

        $member = RegistrationMember::first();
        $this->assertNotNull($member);
        $this->assertEquals('3201234567890001', $member->nik);
        $this->assertEquals('3201234567890002', $member->no_kk);
        $this->assertEquals('BANDUNG', $member->birth_place);
        $this->assertEquals('1990-05-15', $member->birth_date->format('Y-m-d'));
        $this->assertEquals('laki-laki', $member->gender);
        $this->assertEquals('Laki-laki', $member->gender_label);
        $this->assertEquals('Jl. Merdeka No. 45, RT 01/RW 02, Bandung, Jawa Barat', $member->address);
    }

    /**
     * Test 2: NIK mengandung huruf ditolak.
     */
    public function test_nik_containing_letters_is_rejected(): void
    {
        $response = $this->actingAs($this->jamaah)
            ->post(route('jamaah.registration.store'), [
                'package_id' => $this->package->id,
                'members' => [
                    $this->validMemberPayload(['nik' => '320123456789000A'])
                ],
            ]);

        $response->assertSessionHasErrors(['members.0.nik']);
    }

    /**
     * Test 3: No. KK mengandung huruf ditolak.
     */
    public function test_no_kk_containing_letters_is_rejected(): void
    {
        $response = $this->actingAs($this->jamaah)
            ->post(route('jamaah.registration.store'), [
                'package_id' => $this->package->id,
                'members' => [
                    $this->validMemberPayload(['no_kk' => '320123ABC4567890'])
                ],
            ]);

        $response->assertSessionHasErrors(['members.0.no_kk']);
    }

    /**
     * Test 4: NIK mengandung simbol / tanda hubung ditolak.
     */
    public function test_nik_containing_symbols_or_dashes_is_rejected(): void
    {
        $response = $this->actingAs($this->jamaah)
            ->post(route('jamaah.registration.store'), [
                'package_id' => $this->package->id,
                'members' => [
                    $this->validMemberPayload(['nik' => '3201-2345-6789-01'])
                ],
            ]);

        $response->assertSessionHasErrors(['members.0.nik']);
    }

    /**
     * Test 5: No. KK mengandung simbol / tanda hubung ditolak.
     */
    public function test_no_kk_containing_symbols_or_dashes_is_rejected(): void
    {
        $response = $this->actingAs($this->jamaah)
            ->post(route('jamaah.registration.store'), [
                'package_id' => $this->package->id,
                'members' => [
                    $this->validMemberPayload(['no_kk' => '3201-2345-6789-02'])
                ],
            ]);

        $response->assertSessionHasErrors(['members.0.no_kk']);
    }

    /**
     * Test 6: NIK mengandung spasi ditolak.
     */
    public function test_nik_containing_spaces_is_rejected(): void
    {
        $response = $this->actingAs($this->jamaah)
            ->post(route('jamaah.registration.store'), [
                'package_id' => $this->package->id,
                'members' => [
                    $this->validMemberPayload(['nik' => '3201 2345 678901'])
                ],
            ]);

        $response->assertSessionHasErrors(['members.0.nik']);
    }

    /**
     * Test 7: No. KK mengandung spasi ditolak.
     */
    public function test_no_kk_containing_spaces_is_rejected(): void
    {
        $response = $this->actingAs($this->jamaah)
            ->post(route('jamaah.registration.store'), [
                'package_id' => $this->package->id,
                'members' => [
                    $this->validMemberPayload(['no_kk' => '3201 2345 678902'])
                ],
            ]);

        $response->assertSessionHasErrors(['members.0.no_kk']);
    }

    /**
     * Test 8: NIK kurang dari 16 digit ditolak.
     */
    public function test_nik_less_than_16_digits_is_rejected(): void
    {
        $response = $this->actingAs($this->jamaah)
            ->post(route('jamaah.registration.store'), [
                'package_id' => $this->package->id,
                'members' => [
                    $this->validMemberPayload(['nik' => '320123456789000'])
                ],
            ]);

        $response->assertSessionHasErrors(['members.0.nik']);
    }

    /**
     * Test 9: NIK lebih dari 16 digit ditolak.
     */
    public function test_nik_more_than_16_digits_is_rejected(): void
    {
        $response = $this->actingAs($this->jamaah)
            ->post(route('jamaah.registration.store'), [
                'package_id' => $this->package->id,
                'members' => [
                    $this->validMemberPayload(['nik' => '32012345678900001'])
                ],
            ]);

        $response->assertSessionHasErrors(['members.0.nik']);
    }

    /**
     * Test 10: Model mutator menjamin hanya angka 0-9 disimpan.
     */
    public function test_model_mutator_sanitizes_nik_and_no_kk(): void
    {
        $member = new RegistrationMember();
        $member->nik = '3201-2345-6789-0001ABC';
        $member->no_kk = '3201 2345 6789 0002XYZ';

        $this->assertEquals('3201234567890001', $member->nik);
        $this->assertEquals('3201234567890002', $member->no_kk);
    }

    /**
     * Test 11: Tempat lahir, Tanggal lahir, Jenis kelamin, dan Alamat wajib diisi sesuai KTP.
     */
    public function test_ktp_identity_fields_are_required(): void
    {
        $response = $this->actingAs($this->jamaah)
            ->post(route('jamaah.registration.store'), [
                'package_id' => $this->package->id,
                'members' => [
                    [
                        'name' => 'Ahmad Fauzi',
                        'relationship' => 'diri_sendiri',
                        'nik' => '3201234567890001',
                        'no_kk' => '3201234567890002',
                        'ktp_file' => $this->createFakeImage('ktp.jpg'),
                        'kk_file' => $this->createFakeImage('kk.jpg'),
                    ]
                ],
            ]);

        $response->assertSessionHasErrors([
            'members.0.birth_place',
            'members.0.birth_date',
            'members.0.gender',
            'members.0.address',
        ]);
    }
}
