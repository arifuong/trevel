<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    public function test_dynamic_routes_return_no_store_cache_headers(): void
    {
        $response = $this->get('/');
        $response->assertOk();
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('no-cache', $response->headers->get('Cache-Control'));
    }

    public function test_login_and_dashboard_return_no_store_cache_headers(): void
    {
        $user = User::factory()->create([
            'role' => 'jamaah',
            'phone_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('jamaah.dashboard'));
        $response->assertOk();
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('no-cache', $response->headers->get('Cache-Control'));
    }

    public function test_admin_dashboard_returns_no_store_cache_headers(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'phone_verified_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertOk();
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('no-cache', $response->headers->get('Cache-Control'));
    }
}
