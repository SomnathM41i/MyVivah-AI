<?php

namespace Tests\Feature;

use App\Models\Platform;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Phase 5A — authenticated app-shell access control: guests are always pushed
 * to login, unverified accounts can't enter, and verified owners can reach
 * every dashboard screen with honest platform-scoped data.
 */
class DashboardAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        foreach (['/dashboard', '/dashboard/services', '/dashboard/integrations', '/dashboard/subscription', '/dashboard/payments', '/dashboard/settings'] as $path) {
            $this->get($path)->assertRedirect(route('login'));
        }
    }

    public function test_unverified_users_are_sent_to_verification(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect(route('verification.notice'));
    }

    public function test_verified_owner_can_reach_all_dashboard_screens(): void
    {
        $user = User::factory()->create(['name' => 'Asha Sharma']);
        Platform::factory()->createdBy($user)->active()->create([
            'name' => 'Namma Matrimony',
            'slug' => 'namma-matrimony',
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Namma Matrimony')
            ->assertSee('Asha Sharma');

        foreach (['/dashboard/services', '/dashboard/integrations', '/dashboard/subscription', '/dashboard/payments', '/dashboard/settings'] as $path) {
            $this->actingAs($user)->get($path)->assertOk()->assertSee('Namma Matrimony');
        }
    }

    public function test_dashboard_handles_an_account_without_a_platform(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Register a platform under Settings');
    }

    public function test_dashboard_includes_an_escape_hatch_to_public_pages(): void
    {
        $user = User::factory()->create();
        Platform::factory()->createdBy($user)->active()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Logout');
    }
}
