<?php

namespace Tests\Feature;

use App\Models\PlatformIntegration;
use App\Services\PlatformOnboardingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ExternalPlatformSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_widget_search_uses_external_api_and_can_create_unmapped_recipient_conversation(): void
    {
        [$platform, $integration, , $bearer] = $this->provision('external-search');
        $this->verifyUser($bearer, 'alice');
        $this->configureSearch($integration);
        $widget = $this->mintWidgetSession($bearer, 'alice');
        Http::fake(['https://matrimony.example.test/api/search*' => fn () => Http::response([
            'success' => true,
            'data' => [['external_user_id' => 'never-opened', 'display_name' => 'New Member', 'profile_photo_url' => 'https://cdn.example.test/p.jpg']],
            'pagination' => ['next_cursor' => null, 'has_more' => false],
        ])]);

        $results = $this->getJson('/api/v1/widget/users/search?q=new&limit=20', ['Authorization' => 'Bearer '.$widget])
            ->assertOk()->assertJsonPath('data.results.0.display_name', 'New Member');
        $this->assertDatabaseMissing('platform_external_user_map', ['platform_id' => $platform->id, 'external_user_id' => 'never-opened']);

        $created = $this->postJson('/api/v1/widget/chat/conversations', ['candidate_token' => $results->json('data.results.0.candidate_token')], ['Authorization' => 'Bearer '.$widget])
            ->assertOk();
        $this->assertDatabaseHas('platform_external_user_map', ['platform_id' => $platform->id, 'external_user_id' => 'never-opened']);

        $recipientWidget = $this->mintWidgetSession($bearer, 'never-opened');
        $this->getJson('/api/v1/widget/chat/conversations', ['Authorization' => 'Bearer '.$recipientWidget])
            ->assertOk()->assertJsonPath('data.0.id', $created->json('data.id'));
    }

    public function test_external_search_paginates_and_rejects_forged_candidate(): void
    {
        [, $integration, , $bearer] = $this->provision('external-search-page');
        $this->verifyUser($bearer, 'alice');
        $this->configureSearch($integration);
        $widget = $this->mintWidgetSession($bearer, 'alice');
        Http::fake(['https://matrimony.example.test/api/search*' => Http::response(['success' => true, 'data' => [['external_user_id' => 'bob', 'display_name' => 'Bob']], 'pagination' => ['next_cursor' => 'page-2', 'has_more' => true]])]);
        $this->getJson('/api/v1/widget/users/search?q=bob', ['Authorization' => 'Bearer '.$widget])->assertOk()->assertJsonPath('data.pagination.next_cursor', 'page-2')->assertJsonPath('data.pagination.has_more', true);
        $this->postJson('/api/v1/widget/chat/conversations', ['candidate_token' => 'forged'], ['Authorization' => 'Bearer '.$widget])->assertStatus(422);
    }

    public function test_invalid_client_search_response_is_rejected(): void
    {
        [, $integration, , $bearer] = $this->provision('external-search-invalid');
        $this->verifyUser($bearer, 'alice');
        $this->configureSearch($integration);
        $widget = $this->mintWidgetSession($bearer, 'alice');
        Http::fake(['https://matrimony.example.test/api/search*' => Http::response(['success' => true, 'data' => ['malformed'], 'pagination' => ['next_cursor' => null, 'has_more' => false]])]);

        $this->getJson('/api/v1/widget/users/search?q=bob', ['Authorization' => 'Bearer '.$widget])
            ->assertStatus(503)->assertJsonPath('error.code', 'SEARCH_UNAVAILABLE');
    }

    public function test_search_candidate_cannot_cross_platforms(): void
    {
        [, $integrationA, , $bearerA] = $this->provision('external-search-candidate-a');
        $this->verifyUser($bearerA, 'alice');
        $this->configureSearch($integrationA);
        $widgetA = $this->mintWidgetSession($bearerA, 'alice');
        Http::fake(['https://matrimony.example.test/api/search*' => fn () => Http::response(['success' => true, 'data' => [['external_user_id' => 'bob', 'display_name' => 'Bob']], 'pagination' => ['next_cursor' => null, 'has_more' => false]])]);
        $candidate = $this->getJson('/api/v1/widget/users/search?q=bob', ['Authorization' => 'Bearer '.$widgetA])->assertOk()->json('data.results.0.candidate_token');

        [, , , $bearerB] = $this->provision('external-search-candidate-b');
        $this->verifyUser($bearerB, 'alice');
        $widgetB = $this->mintWidgetSession($bearerB, 'alice');
        $this->postJson('/api/v1/widget/chat/conversations', ['candidate_token' => $candidate], ['Authorization' => 'Bearer '.$widgetB])
            ->assertStatus(422)->assertJsonPath('error.code', 'INVALID_SEARCH_RESULT');
    }

    public function test_search_endpoint_rejects_ip_literal_destination(): void
    {
        [, $integration, , $bearer] = $this->provision('external-search-ssrf');
        $this->verifyUser($bearer, 'alice');
        $integration->forceFill([
            'user_search_endpoint' => 'https://127.0.0.1/api/search',
            'user_search_allowed_hosts' => ['127.0.0.1'],
            'user_search_auth_type' => 'bearer',
            'user_search_auth_secret' => Crypt::encryptString('test-secret'),
        ])->save();
        $widget = $this->mintWidgetSession($bearer, 'alice');

        $this->getJson('/api/v1/widget/users/search?q=bob', ['Authorization' => 'Bearer '.$widget])
            ->assertStatus(503)->assertJsonPath('error.code', 'SEARCH_NOT_CONFIGURED');
        Http::assertNothingSent();
    }

    public function test_search_endpoint_failure_does_not_break_existing_conversation_api(): void
    {
        [, $integration, , $bearer] = $this->provision('external-search-failure');
        $this->verifyUser($bearer, 'alice');
        $this->configureSearch($integration);
        $widget = $this->mintWidgetSession($bearer, 'alice');
        Http::fake(['https://matrimony.example.test/api/search*' => Http::response([], 503)]);
        $this->getJson('/api/v1/widget/users/search?q=bob', ['Authorization' => 'Bearer '.$widget])->assertStatus(503)->assertJsonPath('error.code', 'SEARCH_UNAVAILABLE');
        $this->getJson('/api/v1/widget/chat/conversations', ['Authorization' => 'Bearer '.$widget])->assertOk();
    }

    public function test_chat_health_is_safe_and_reports_only_database_readiness(): void
    {
        $this->getJson('/api/v1/health/chat')->assertOk()->assertJsonPath('data.status', 'ok')->assertJsonPath('data.checks.database', true);
    }

    public function test_target_is_rechecked_and_blocked_result_does_not_create_mapping_or_conversation(): void
    {
        [$platform, $integration, , $bearer] = $this->provision('external-search-blocked');
        $this->verifyUser($bearer, 'alice');
        $this->configureSearch($integration);
        $widget = $this->mintWidgetSession($bearer, 'alice');
        Http::fake(['https://matrimony.example.test/api/search*' => Http::sequence()
            ->push(['success' => true, 'data' => [['external_user_id' => 'bob', 'display_name' => 'Bob']], 'pagination' => ['next_cursor' => null, 'has_more' => false]])
            ->push(['success' => true, 'data' => [], 'pagination' => ['next_cursor' => null, 'has_more' => false]])]);
        $search = $this->getJson('/api/v1/widget/users/search?q=bob', ['Authorization' => 'Bearer '.$widget])->assertOk();
        $this->postJson('/api/v1/widget/chat/conversations', ['candidate_token' => $search->json('data.results.0.candidate_token')], ['Authorization' => 'Bearer '.$widget])
            ->assertStatus(403)->assertJsonPath('error.code', 'CHAT_NOT_ALLOWED');
        $this->assertDatabaseMissing('platform_external_user_map', ['platform_id' => $platform->id, 'external_user_id' => 'bob']);
        $this->assertDatabaseCount('conversations', 0);
        Http::assertSent(fn ($request) => $request->hasHeader('Authorization', 'Bearer test-secret') && $request->hasHeader('X-MyVivahAI-Requester-ID', 'alice'));
    }

    private function configureSearch(PlatformIntegration $integration): void
    {
        $integration->forceFill([
            'base_domain' => 'https://matrimony.example.test',
            'allowed_origins' => ['https://matrimony.example.test'],
            'user_search_allowed_hosts' => ['matrimony.example.test'],
            'user_search_endpoint' => 'https://matrimony.example.test/api/search',
            'user_search_auth_type' => 'bearer',
            'user_search_auth_secret' => Crypt::encryptString('test-secret'),
        ])->save();
    }

    private function provision(string $slug): array
    {
        [$platform, $integration, $secret] = $this->app->make(PlatformOnboardingService::class)->onboard($slug);
        $response = $this->postJson('/api/v1/auth/token', ['client_id' => $platform->public_id, 'client_secret' => (string) $secret])->assertOk();

        return [$platform, $integration, $secret, (string) $response->json('data.access_token')];
    }

    private function verifyUser(string $bearer, string $externalUserId): void
    {
        $this->postJson('/api/v1/users/verify', ['external_user_id' => $externalUserId], ['Authorization' => 'Bearer '.$bearer])->assertOk();
    }

    private function mintWidgetSession(string $bearer, string $externalUserId): string
    {
        return (string) $this->postJson('/api/v1/widget/session', ['external_user_id' => $externalUserId], ['Authorization' => 'Bearer '.$bearer])->assertOk()->json('data.access_token');
    }
}
