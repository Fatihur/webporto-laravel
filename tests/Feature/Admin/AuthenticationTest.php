<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_login_page_can_be_accessed(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
        $response->assertSee('Login');
    }

    #[Test]
    public function authenticated_user_can_access_admin_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/admin');

        $response->assertStatus(200);
    }

    #[Test]
    public function guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/admin/login');
    }

    #[Test]
    public function user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        Livewire::test(\App\Livewire\Admin\Auth\Login::class)
            ->set('email', 'admin@example.com')
            ->set('password', 'password')
            ->call('login')
            ->assertRedirect('/admin');

        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function user_cannot_login_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        Livewire::test(\App\Livewire\Admin\Auth\Login::class)
            ->set('email', 'admin@example.com')
            ->set('password', 'wrong-password')
            ->call('login')
            ->assertHasErrors(['email']);

        $this->assertGuest();
    }

    #[Test]
    public function login_requires_email(): void
    {
        Livewire::test(\App\Livewire\Admin\Auth\Login::class)
            ->set('email', '')
            ->set('password', 'password')
            ->call('login')
            ->assertHasErrors(['email']);
    }

    #[Test]
    public function login_requires_password(): void
    {
        Livewire::test(\App\Livewire\Admin\Auth\Login::class)
            ->set('email', 'admin@example.com')
            ->set('password', '')
            ->call('login')
            ->assertHasErrors(['password']);
    }

    #[Test]
    public function authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post('/admin/logout');

        $response->assertRedirect('/admin/login');
        $this->assertGuest();
    }

    #[Test]
    public function guest_cannot_access_admin_projects(): void
    {
        $response = $this->get('/admin/projects');

        $response->assertRedirect('/admin/login');
    }

    #[Test]
    public function guest_cannot_access_admin_blogs(): void
    {
        $response = $this->get('/admin/blogs');

        $response->assertRedirect('/admin/login');
    }

    #[Test]
    public function guest_cannot_access_admin_contacts(): void
    {
        $response = $this->get('/admin/contacts');

        $response->assertRedirect('/admin/login');
    }

    #[Test]
    public function guest_cannot_access_admin_experiences(): void
    {
        $response = $this->get('/admin/experiences');

        $response->assertRedirect('/admin/login');
    }
}
