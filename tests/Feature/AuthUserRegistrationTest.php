<?php

namespace Tests\Feature;

use App\Models\Platform;
use App\Models\PlatformAdmin;
use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

/**
 * Phase 5A — self-service registration (registration-flow.md §2/§4): the
 * signup button creates an unverified user + active platform + owner admin
 * atomically, gate email verification, and reject duplicates.
 */
class AuthUserRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_signup_creates_user_platform_and_owner_admin(): void
    {
        Notification::fake();

        $this->post('/signup', [
            'name' => 'Asha Sharma',
            'email' => 'asha@namma.example',
            'password' => 'StrongPass123!',
            'password_confirmation' => 'StrongPass123!',
            'platform_name' => 'Namma Matrimony',
            'website_url' => 'https://namma-matrimony.example',
            'terms' => '1',
        ])->assertRedirect(route('verification.notice'))
            ->assertSessionHas('status');

        $user = User::query()->where('email', 'asha@namma.example')->firstOrFail();
        $this->assertNull($user->email_verified_at);
        $this->assertSame('active', $user->status);
        $this->assertSame('en', $user->locale);
        $this->assertSame('UTC', $user->timezone);

        $platform = Platform::query()->where('name', 'Namma Matrimony')->firstOrFail();
        $this->assertSame('namma-matrimony', $platform->slug);
        $this->assertSame(Platform::STATUS_ACTIVE, $platform->status);
        $this->assertSame('https://namma-matrimony.example', $platform->website_url);
        $this->assertSame($user->id, $platform->created_by);

        $admin = PlatformAdmin::query()->where('platform_id', $platform->id)->firstOrFail();
        $this->assertSame($user->id, $admin->user_id);
        $this->assertSame('owner', $admin->role);

        Notification::assertSentTo($user, VerifyEmailNotification::class);
    }

    public function test_signup_requires_accepted_terms(): void
    {
        $this->post('/signup', [
            'name' => 'No Terms Guy',
            'email' => 'skeptic@example.test',
            'password' => 'StrongPass123!',
            'password_confirmation' => 'StrongPass123!',
            'platform_name' => 'No Terms Platform',
        ])->assertSessionHasErrors('terms');

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('platforms', 0);
    }

    public function test_duplicate_email_is_rejected(): void
    {
        User::factory()->create(['email' => 'owner@example.test']);

        $this->post('/signup', [
            'name' => 'Second Owner',
            'email' => 'owner@example.test',
            'password' => 'StrongPass123!',
            'password_confirmation' => 'StrongPass123!',
            'platform_name' => 'Duplicate Platform',
            'terms' => '1',
        ])->assertSessionHasErrors('email');

        $this->assertDatabaseCount('platforms', 0);
    }

    public function test_platform_slugs_are_unique_when_names_collide(): void
    {
        $this->post('/signup', [
            'name' => 'Owner A', 'email' => 'a@example.test',
            'password' => 'StrongPass123!', 'password_confirmation' => 'StrongPass123!',
            'platform_name' => 'Same Name', 'terms' => '1',
        ])->assertRedirect();

        $this->post('/signup', [
            'name' => 'Owner B', 'email' => 'b@example.test',
            'password' => 'StrongPass123!', 'password_confirmation' => 'StrongPass123!',
            'platform_name' => 'Same Name', 'terms' => '1',
        ])->assertRedirect();

        $slugs = Platform::query()->orderBy('id')->pluck('slug')->all();
        $this->assertSame(['same-name', 'same-name-2'], $slugs);
    }

    public function test_password_must_match_confirmation(): void
    {
        $this->post('/signup', [
            'name' => 'Owner A', 'email' => 'a@example.test',
            'password' => 'StrongPass123!', 'password_confirmation' => 'Different123!',
            'platform_name' => 'Platform', 'terms' => '1',
        ])->assertSessionHasErrors('password');

        $this->assertDatabaseCount('users', 0);
    }

    public function test_resend_endpoint_never_enumerates_accounts(): void
    {
        Notification::fake();
        User::factory()->unverified()->create(['email' => 'verify@example.test']);

        // Unknown email → same neutral success message as a real one.
        $this->post('/verify-email/resend', ['email' => 'ghost@example.test'])
            ->assertRedirect()
            ->assertSessionHas('status');

        Notification::assertNothingSent();

        // Known unverified email → a fresh verification link is queued.
        $user = User::query()->where('email', 'verify@example.test')->firstOrFail();
        $this->post('/verify-email/resend', ['email' => 'verify@example.test'])
            ->assertRedirect()
            ->assertSessionHas('status');

        Notification::assertSentTo($user, VerifyEmailNotification::class);
    }

    public function test_signed_verification_link_verifies_and_signs_in(): void
    {
        $user = User::factory()->unverified()->create(['email' => 'verify@example.test']);
        $this->assertFalse($user->hasVerifiedEmail());

        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
            'id' => $user->getKey(),
            'hash' => sha1($user->getEmailForVerification()),
        ]);

        $this->get($url)
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->assertTrue($user->refresh()->hasVerifiedEmail());
    }

    public function test_unsigned_or_tampered_verification_links_are_rejected(): void
    {
        $user = User::factory()->unverified()->create();

        $this->get(route('verification.verify', [
            'id' => $user->getKey(),
            'hash' => sha1('someone-else@example.test'),
        ]))->assertStatus(403);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }
}
