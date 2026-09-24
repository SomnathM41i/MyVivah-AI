<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Phase 5A — web login: only verified, active accounts sign in; suspended and
 * unverified accounts get distinct, helpful outcomes; no account enumeration.
 */
class AuthLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_verified_active_user_can_sign_in(): void
    {
        $user = User::factory()->create(['email' => 'asha@namma.example', 'password' => 'SecretPass1!']);

        $this->post('/login', [
            'email' => 'asha@namma.example',
            'password' => 'SecretPass1!',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->last_login_at);
    }

    public function test_wrong_password_is_rejected_with_generic_message(): void
    {
        User::factory()->create(['email' => 'asha@namma.example']);

        $this->post('/login', [
            'email' => 'asha@namma.example',
            'password' => 'WrongPassword1!',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_unknown_email_is_rejected_with_generic_message(): void
    {
        $this->post('/login', [
            'email' => 'ghost@example.test',
            'password' => 'Whatever123!',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_unverified_user_is_redirected_to_verification_notice(): void
    {
        User::factory()->unverified()->create(['email' => 'pending@example.test']);

        $this->post('/login', [
            'email' => 'pending@example.test',
            'password' => 'password',
        ])->assertRedirect(route('verification.notice'))
            ->assertSessionHas('status');

        $this->assertGuest();
    }

    public function test_suspended_user_is_blocked(): void
    {
        User::factory()->create(['email' => 'blocked@example.test', 'status' => 'suspended']);

        $this->post('/login', [
            'email' => 'blocked@example.test',
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_deactivated_user_is_blocked(): void
    {
        User::factory()->create(['email' => 'gone@example.test', 'status' => 'deactivated']);

        $this->post('/login', [
            'email' => 'gone@example.test',
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_logged_in_user_visiting_login_is_redirected_away(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/login')->assertRedirect(route('dashboard'));
    }

    public function test_login_is_throttled(): void
    {
        for ($i = 0; $i < 7; $i++) {
            $this->post('/login', [
                'email' => 'spam@example.test',
                'password' => 'WrongPass123!',
            ]);
        }

        $this->post('/login', [
            'email' => 'spam@example.test',
            'password' => 'WrongPass123!',
        ])->assertStatus(429);

        $this->assertGuest();
    }

    public function test_logout_ends_the_session(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect(route('home'))
            ->assertSessionHas('success');

        $this->assertGuest();
    }
}
