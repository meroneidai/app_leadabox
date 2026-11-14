<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_the_landing_page(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_first_account_can_register_and_becomes_admin(): void
    {
        $this->seedRoles();

        $response = $this->post('/register', [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'password' => 'password-123',
            'password_confirmation' => 'password-123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertTrue(User::firstWhere('email', 'ada@example.com')->isAdmin());
    }

    public function test_registration_closes_once_an_account_exists(): void
    {
        $this->staff(Role::Admin);

        $this->get('/register')->assertForbidden();
        $this->post('/register', [
            'name' => 'Intruder',
            'email' => 'intruder@example.com',
            'password' => 'password-123',
            'password_confirmation' => 'password-123',
        ])->assertForbidden();
    }

    public function test_user_can_log_in_and_out(): void
    {
        $user = $this->staff(Role::Admin);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->last_login_at);

        $this->post('/logout')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_login_fails_with_bad_password(): void
    {
        $user = $this->staff(Role::Admin);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_deactivated_user_cannot_log_in(): void
    {
        $user = $this->staff(Role::Sales, ['is_active' => false]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_dashboard_requires_authentication(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_locale_can_be_switched(): void
    {
        $user = $this->staff(Role::Admin);

        $this->actingAs($user)->put(route('locale.update', 'sv'));

        $this->assertSame('sv', $user->fresh()->locale);
    }

    public function test_unsupported_locale_is_rejected(): void
    {
        $this->actingAs($this->staff(Role::Admin))
            ->put(route('locale.update', 'de'))
            ->assertNotFound();
    }
}
