<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/daftar');
        $response->assertStatus(200);
        $response->assertSee('Daftar Akun Jamaah');
    }

    public function test_new_users_can_register_and_receive_otp(): void
    {
        $response = $this->post('/daftar', [
            'name' => 'Fulan bin Fulan',
            'email' => 'fulan@example.com',
            'phone' => '081234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/verifikasi-otp');
        $this->assertDatabaseHas('users', [
            'email' => 'fulan@example.com',
            'phone' => '6281234567890',
            'role' => 'jamaah',
            'phone_verified_at' => null,
        ]);

        $user = User::where('email', 'fulan@example.com')->first();
        $this->assertNotNull($user->otp_code);
        $this->assertNotNull($user->otp_expires_at);
    }

    public function test_registration_fails_on_duplicate_email(): void
    {
        User::create([
            'name' => 'Existing User',
            'email' => 'existing@example.com',
            'phone' => '628111111111',
            'password' => 'password123',
            'role' => 'jamaah',
            'phone_verified_at' => now(),
        ]);

        $response = $this->post('/daftar', [
            'name' => 'New User',
            'email' => 'existing@example.com',
            'phone' => '081222222222',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_registration_fails_on_duplicate_phone(): void
    {
        User::create([
            'name' => 'Existing User',
            'email' => 'user1@example.com',
            'phone' => '081234567890',
            'password' => 'password123',
            'role' => 'jamaah',
            'phone_verified_at' => now(),
        ]);

        $response = $this->post('/daftar', [
            'name' => 'New User',
            'email' => 'user2@example.com',
            'phone' => '081234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('phone');
    }

    public function test_registration_fails_on_password_mismatch(): void
    {
        $response = $this->post('/daftar', [
            'name' => 'New User',
            'email' => 'new@example.com',
            'phone' => '081234567890',
            'password' => 'password123',
            'password_confirmation' => 'mismatch999',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_user_can_verify_otp_and_login(): void
    {
        $user = User::create([
            'name' => 'Fulan bin Fulan',
            'email' => 'fulan@example.com',
            'phone' => '6281234567890',
            'password' => 'password123',
            'role' => 'jamaah',
            'otp_code' => '123456',
            'otp_expires_at' => now()->addMinutes(5),
        ]);

        $response = $this->withSession(['otp_user_id' => $user->id])
            ->post('/verifikasi-otp', [
                'otp' => '123456',
            ]);

        $response->assertRedirect('/jamaah/dashboard');
        $this->assertAuthenticated();
        $this->assertNotNull($user->fresh()->phone_verified_at);
        $this->assertNull($user->fresh()->otp_code);
    }

    public function test_user_cannot_verify_with_wrong_otp(): void
    {
        $user = User::create([
            'name' => 'Fulan bin Fulan',
            'email' => 'fulan@example.com',
            'phone' => '6281234567890',
            'password' => 'password123',
            'role' => 'jamaah',
            'otp_code' => '123456',
            'otp_expires_at' => now()->addMinutes(5),
        ]);

        $response = $this->withSession(['otp_user_id' => $user->id])
            ->post('/verifikasi-otp', [
                'otp' => '654321',
            ]);

        $response->assertSessionHasErrors('otp');
        $this->assertGuest();
        $this->assertNull($user->fresh()->phone_verified_at);
    }

    public function test_user_can_resend_otp(): void
    {
        $user = User::create([
            'name' => 'Fulan bin Fulan',
            'email' => 'fulan@example.com',
            'phone' => '6281234567890',
            'password' => 'password123',
            'role' => 'jamaah',
            'otp_code' => '111111',
            'otp_expires_at' => now()->addMinutes(1),
        ]);

        $response = $this->withSession(['otp_user_id' => $user->id])
            ->post('/kirim-ulang-otp');

        $response->assertRedirect();
        $this->assertNotEquals('111111', $user->fresh()->otp_code);
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Masuk ke Akun Anda');
    }

    public function test_login_fails_on_unregistered_email(): void
    {
        $response = $this->post('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_fails_on_wrong_password(): void
    {
        User::create([
            'name' => 'Fulan bin Fulan',
            'email' => 'fulan@example.com',
            'phone' => '6281234567890',
            'password' => 'password123',
            'role' => 'jamaah',
            'phone_verified_at' => now(),
        ]);

        $response = $this->post('/login', [
            'email' => 'fulan@example.com',
            'password' => 'wrongpass999',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_jamaah_cannot_login_if_otp_not_verified(): void
    {
        $user = User::create([
            'name' => 'Fulan bin Fulan',
            'email' => 'fulan@example.com',
            'phone' => '6281234567890',
            'password' => 'password123',
            'role' => 'jamaah',
            'phone_verified_at' => null,
            'otp_code' => '123456',
            'otp_expires_at' => now()->addMinutes(5),
        ]);

        $response = $this->post('/login', [
            'email' => 'fulan@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/verifikasi-otp');
        $this->assertGuest();
    }

    public function test_verified_jamaah_can_login(): void
    {
        $user = User::create([
            'name' => 'Fulan bin Fulan',
            'email' => 'fulan@example.com',
            'phone' => '6281234567890',
            'password' => 'password123',
            'role' => 'jamaah',
            'phone_verified_at' => now(),
        ]);

        $response = $this->post('/login', [
            'email' => 'fulan@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/jamaah/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_cannot_login_via_jamaah_login(): void
    {
        $admin = User::create([
            'name' => 'Admin Zein',
            'email' => 'admin@zeintour.com',
            'phone' => '6282121483337',
            'password' => 'admin123',
            'role' => 'admin',
            'phone_verified_at' => now(),
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@zeintour.com',
            'password' => 'admin123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_can_login_via_admin_login(): void
    {
        $admin = User::create([
            'name' => 'Admin Zein',
            'email' => 'admin@zeintour.com',
            'phone' => '6282121483337',
            'password' => 'admin123',
            'role' => 'admin',
            'phone_verified_at' => now(),
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'admin@zeintour.com',
            'password' => 'admin123',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($admin);
    }

    public function test_non_admin_cannot_access_admin_dashboard(): void
    {
        $user = User::create([
            'name' => 'Fulan bin Fulan',
            'email' => 'fulan@example.com',
            'phone' => '6281234567890',
            'password' => 'password123',
            'role' => 'jamaah',
            'phone_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertRedirect('/admin/login');
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::create([
            'name' => 'Fulan bin Fulan',
            'email' => 'fulan@example.com',
            'phone' => '6281234567890',
            'password' => 'password123',
            'role' => 'jamaah',
            'phone_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }
}
