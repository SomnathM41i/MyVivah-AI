<?php

namespace Tests\Feature;

use App\Models\Platform;
use App\Models\PlatformIntegration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
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

    public function test_platform_owner_can_save_search_configuration_without_revealing_secret(): void
    {
        $user = User::factory()->create();
        $platform = Platform::factory()->createdBy($user)->active()->create();
        $integration = PlatformIntegration::factory()->forPlatform($platform)->active()->create();

        $this->actingAs($user)->put(route('dashboard.integrations.user-search.update'), [
            'user_search_endpoint' => 'https://matrimony.example.test/api/search',
            'user_search_auth_type' => 'bearer',
            'user_search_auth_secret' => 'never-display-this',
            'allowed_search_origin' => 'https://matrimony.example.test',
        ])->assertRedirect()->assertSessionHas('status');

        $saved = $integration->fresh();
        $this->assertSame('https://matrimony.example.test/api/search', $saved->user_search_endpoint);
        $this->assertSame('matrimony.example.test', $saved->user_search_allowed_hosts[0]);
        $this->assertNotSame('never-display-this', $saved->user_search_auth_secret);
        $this->assertSame('never-display-this', Crypt::decryptString($saved->user_search_auth_secret));
        $this->actingAs($user)->get('/dashboard/integrations')->assertOk()->assertDontSee('never-display-this');
    }

    public function test_dashboard_search_connection_test_reports_only_safe_status(): void
    {
        $user = User::factory()->create();
        $platform = Platform::factory()->createdBy($user)->active()->create();
        PlatformIntegration::factory()->forPlatform($platform)->active()->create([
            'user_search_endpoint' => 'https://matrimony.example.test/api/search',
            'user_search_allowed_hosts' => ['matrimony.example.test'],
            'user_search_auth_type' => 'bearer',
            'user_search_auth_secret' => Crypt::encryptString('search-secret'),
        ]);
        Http::fake(['https://matrimony.example.test/api/search*' => Http::response(['success' => true, 'data' => [], 'pagination' => ['next_cursor' => null, 'has_more' => false]])]);

        $this->actingAs($user)->post(route('dashboard.integrations.user-search.test'), [
            'requester_external_user_id' => 'member-1',
            'query' => 'sample',
        ])->assertRedirect()->assertSessionHas('search_test_status', 'Connection succeeded; no eligible users for this sample query.');
    }
}
