<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OcrControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $jamaah;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jamaah = User::factory()->create([
            'role' => 'jamaah',
        ]);
    }

    public function test_guest_cannot_access_ocr_parse_endpoint()
    {
        $response = $this->postJson(route('jamaah.ocr.parse'), [
            'doc_type' => 'ktp',
            'raw_text' => 'NIK : 3204123456780001',
        ]);

        $response->assertUnauthorized();
    }

    public function test_authenticated_jamaah_can_parse_ktp_ocr()
    {
        $response = $this->actingAs($this->jamaah)->postJson(route('jamaah.ocr.parse'), [
            'doc_type' => 'ktp',
            'raw_text' => "NIK : 3204123456780001\nNama : SITI AMINAH\nTempat/Tgl Lahir : BANDUNG, 10-10-1992",
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'type' => 'ktp',
            'data' => [
                'nik' => '3204123456780001',
                'name' => 'SITI AMINAH',
                'birth_place' => 'BANDUNG',
                'birth_date' => '1992-10-10',
            ],
        ]);
    }

    public function test_authenticated_jamaah_can_parse_kk_ocr()
    {
        $response = $this->actingAs($this->jamaah)->postJson(route('jamaah.ocr.parse'), [
            'doc_type' => 'kk',
            'raw_text' => "KARTU KELUARGA\nNo. 3204987654320005",
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'type' => 'kk',
            'data' => [
                'no_kk' => '3204987654320005',
            ],
        ]);
    }

    public function test_ocr_parse_validates_required_fields()
    {
        $response = $this->actingAs($this->jamaah)->postJson(route('jamaah.ocr.parse'), [
            'doc_type' => 'invalid_doc',
        ]);

        $response->assertUnprocessable();
    }
}
