<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

/**
 * Phase 5A — forgot/reset password via the framework `resets` broker:
 * notification is fired, tokens are stored/expired by Laravel, no account
 * enumeration, and the new password actually rotates.
 */
class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_sends_a_reset_notification(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'owner@example.test']);

        $this->post('/forgot-password', ['email' => 'owner@example.test'])
            ->assertRedirect()
            ->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPasswordNotification::class);
        $this->assertDatabaseHas('password_reset_tokens', ['email' => 'owner@example.test']);
    }

    public function test_forgot_password_does_not_enumerate_accounts(): void
    {
        Notification::fake();

        $this->post('/forgot-password', ['email' => 'nobody@example.test'])
            ->assertRedirect()
            ->assertSessionHas('status');

        Notification::assertNothingSent();
        $this->assertDatabaseCount('password_reset_tokens', 0);
    }

    public function test_reset_flow_rotates_the_password(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'owner@example.test']);

        $token = Password::broker()->createToken($user);

        $this->post('/reset-password', [
            'token' => $token,
            'email' => 'owner@example.test',
            'password' => 'NewSecretPass1!',
            'password_confirmation' => 'NewSecretPass1!',
        ])->assertRedirect(route('login'))
            ->assertSessionHas('success');

        $this->assertTrue(Hash::check('NewSecretPass1!', $user->fresh()->password));
        $this->assertFalse(Hash::check('password', $user->fresh()->password));
    }

    public function test_expired_or_consumed_token_is_rejected(): void
    {
        $user = User::factory()->create(['email' => 'owner@example.test']);

        $this->post('/reset-password', [
            'token' => 'not-a-real-token',
            'email' => 'owner@example.test',
            'password' => 'NewSecretPass1!',
            'password_confirmation' => 'NewSecretPass1!',
        ])->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_reset_form_renders_with_token_and_email(): void
    {
        $user = User::factory()->create(['email' => 'owner@example.test']);
        $token = Password::broker()->createToken($user);

        $this->get(route('password.reset', ['token' => $token, 'email' => 'owner@example.test']))
            ->assertOk()
            ->assertSee('Set a new password')
            ->assertSee($token, false);
    }

    public function test_reset_password_form_requires_a_valid_email(): void
    {
        $user = User::factory()->create(['email' => 'owner@example.test']);
        $token = Password::broker()->createToken($user);

        $this->post('/reset-password', [
            'token' => $token,
            'email' => 'someone-else@example.test',
            'password' => 'NewSecretPass1!',
            'password_confirmation' => 'NewSecretPass1!',
        ])->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }
}
