<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    protected function createAdmin(): User
    {
        return User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);
    }

    public function test_show_login_returns_view(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    public function test_login_with_valid_credentials_redirects_to_dashboard(): void
    {
        $this->createAdmin();

        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_login_with_invalid_credentials_redirects_back_with_error(): void
    {
        $this->createAdmin();

        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['username' => 'Username atau password salah']);
        $response->assertSessionHasInput('username', 'admin');
        $this->assertGuest();
    }

    public function test_login_validates_username_required(): void
    {
        $response = $this->post('/login', [
            'username' => '',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_login_validates_password_required(): void
    {
        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_login_validates_password_min_6_characters(): void
    {
        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'abc',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_login_validates_password_max_128_characters(): void
    {
        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => str_repeat('a', 129),
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_rate_limiting_blocks_after_5_failed_attempts(): void
    {
        $this->createAdmin();

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'username' => 'admin',
                'password' => 'wrongpassword',
            ]);
        }

        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['username' => 'Terlalu banyak percobaan. Silakan coba lagi dalam 60 detik.']);
    }

    public function test_successful_login_clears_rate_limiter(): void
    {
        $this->createAdmin();

        for ($i = 0; $i < 3; $i++) {
            $this->post('/login', [
                'username' => 'admin',
                'password' => 'wrongpassword',
            ]);
        }

        $this->post('/login', [
            'username' => 'admin',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();

        $this->post('/logout');

        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['username' => 'Username atau password salah']);
    }

    public function test_logout_invalidates_session_and_redirects_to_login(): void
    {
        $user = $this->createAdmin();

        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}
