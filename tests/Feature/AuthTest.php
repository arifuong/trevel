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

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/daftar', [
            'name' => 'Fulan bin Fulan',
            'email' => 'fulan@example.com',
            'phone' => '081234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/jamaah/dashboard');
        $this->assertDatabaseHas('users', [
            'email' => 'fulan@example.com',
            'phone' => '6281234567890',
            'role' => 'jamaah',
        ]);
        $this->assertAuthenticated();
    }

    public function test_registration_fails_on_duplicate_email(): void
    {
        User::create([
            'name' => 'Existing User',
            'email' => 'existing@example.com',
            'phone' => '628111111111',
            'password' => 'password123',
            'role' => 'jamaah',
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
        ]);

        $response = $this->post('/login', [
            'email' => 'fulan@example.com',
            'password' => 'wrongpass999',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_jamaah_can_login_with_email_and_password(): void
    {
        $user = User::create([
            'name' => 'Fulan bin Fulan',
            'email' => 'fulan@example.com',
            'phone' => '6281234567890',
            'password' => 'password123',
            'role' => 'jamaah',
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
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }
}
