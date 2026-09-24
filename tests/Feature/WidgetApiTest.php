<?php

namespace Tests\Feature;

use App\Models\Platform;
use App\Models\PlatformIntegration;
use App\Services\PasetoTokenService;
use App\Services\PlatformOnboardingService;
use App\Services\WidgetSessionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Phase 4 widget session bootstrap + token-trust model.
 *
 * The security contract under test: ONLY a platform backend can mint a widget
 * session (server-to-server, write scope), widget tokens are single-user and
 * single-platform, identity always comes from the verified token (never from
 * browser-supplied headers/claims), and platform-token ↔ widget-token routes can
 * never be crossed.
 */
class WidgetApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_widget_session_endpoint_mints_a_short_lived_v4_local_token(): void
    {
        [$platform, $integration, , $bearer] = $this->provision('widget-mint');
        $this->verifyUser($bearer, 'alice@demo.example.test');

        $response = $this->postJson('/api/v1/widget/session', [
            'external_user_id' => 'alice@demo.example.test',
        ], ['Authorization' => 'Bearer '.$bearer]);

        $response->assertOk()
            ->assertJsonPath('data.token_type', 'Widget')
            ->assertJsonPath('data.self.external_user_id', 'alice@demo.example.test');

        $token = (string) $response->json('data.access_token');
        $this->assertStringStartsWith('v4.local.', $token);

        $expiresIn = (int) $response->json('data.expires_in');
        $this->assertGreaterThanOrEqual(1, $expiresIn);
        $this->assertLessThanOrEqual((int) config('widget.session.max_ttl_seconds'), $expiresIn);

        $this->assertSame('private-chat', $response->json('data.channels.private'));
        $this->assertSame('presence-chat', $response->json('data.channels.presence'));
        $this->assertIsArray($response->json('data.realtime'));
        $this->assertArrayHasKey('enabled', $response->json('data.realtime'));
        $this->assertArrayHasKey('connection', $response->json('data.realtime'));

        // The mint endpoint maps the user (server-side upsert). Direct DB check:
        $this->assertDatabaseHas('platform_external_user_map', [
            'platform_id' => $platform->id,
            'external_user_id' => 'alice@demo.example.test',
        ]);
    }

    public function test_widget_session_requires_platform_authentication(): void
    {
        $this->postJson('/api/v1/widget/session', ['external_user_id' => 'alice@demo.example.test'])
            ->assertStatus(401)
            ->assertJsonPath('error.code', 'INVALID_TOKEN');
    }

    public function test_widget_session_requires_the_realtime_chat_write_scope(): void
    {
        [$platform, $integration, , $bearer] = $this->provision('widget-scope');

        $this->postJson('/api/v1/widget/session', ['external_user_id' => 'bob@demo.example.test'], [
            'Authorization' => 'Bearer '.$bearer,
        ])->assertStatus(200); // full/provisioned platform token holds write scope

        $readOnly = $this->mintPlatformToken($platform, $integration, [
            config('paseto.authentication_scope'),
            'realtime_chat:read',
        ]);

        $this->postJson('/api/v1/widget/session', ['external_user_id' => 'bob@demo.example.test'], [
            'Authorization' => 'Bearer '.$readOnly,
        ])->assertStatus(403)
            ->assertJsonPath('error.code', 'SERVICE_NO_ACCESS');
    }

    public function test_widget_token_works_on_widget_routes_but_not_platform_routes(): void
    {
        [$platform, , , $bearer] = $this->provision('widget-cross');
        $this->verifyUser($bearer, 'alice@demo.example.test');

        $widgetToken = $this->mintWidgetSession($bearer, 'alice@demo.example.test');

        $this->getJson('/api/v1/widget/chat/conversations', [
            'Authorization' => 'Bearer '.$widgetToken,
        ])->assertOk()
            ->assertJsonPath('meta.pagination.total', 0);

        $this->getJson('/api/v1/chat/conversations', [
            'Authorization' => 'Bearer '.$widgetToken,
        ])->assertStatus(403)
            ->assertJsonPath('error.code', 'PLATFORM_MISMATCH');
    }

    public function test_platform_tokens_are_rejected_on_widget_routes(): void
    {
        [, , , $bearer] = $this->provision('widget-reverse');

        $this->getJson('/api/v1/widget/chat/conversations', [
            'Authorization' => 'Bearer '.$bearer,
        ])->assertStatus(403)
            ->assertJsonPath('error.code', 'PLATFORM_MISMATCH');
    }

    public function test_widget_identity_comes_from_the_token_not_the_header(): void
    {
        [$platform, , , $bearer] = $this->provision('widget-spoof');
        $this->verifyUser($bearer, 'alice@demo.example.test');
        $this->verifyUser($bearer, 'bob@demo.example.test');

        // A thread between alice and carol — bob is deliberately NOT a participant.
        $this->verifyUser($bearer, 'carol@demo.example.test');
        [$threadA] = $this->makeConversation($platform, $bearer, ['alice@demo.example.test', 'carol@demo.example.test']);
        [$threadB] = $this->makeConversation($platform, $bearer, ['alice@demo.example.test', 'bob@demo.example.test']);

        $bobWidget = $this->mintWidgetSession($bearer, 'bob@demo.example.test');

        $response = $this->getJson('/api/v1/widget/chat/conversations', [
            'Authorization' => 'Bearer '.$bobWidget,
            'X-External-User-Id' => 'alice@demo.example.test', // spoofed
        ]);

        $response->assertOk();
        $ids = collect($response->json('data'))->pluck('id')->all();
        $this->assertNotContains($threadA, $ids, 'bob must NOT see alice+carol via a spoofed header');
        $this->assertContains($threadB, $ids, 'bob must see his own alice+bob thread via token identity');

        // The spoofed header must also not change a single-user listing.
        $this->assertNotContains(
            $threadA,
            collect($this->getJson('/api/v1/widget/chat/conversations', [
                'Authorization' => 'Bearer '.$bobWidget,
                'X-External-User-Id' => 'carol@demo.example.test',
            ])->json('data'))->pluck('id')->all(),
            'a widget caller can never see another user\'s threads'
        );
    }

    public function test_integration_user_search_is_platform_scoped_and_like_escaped(): void
    {
        [$platform, , , $bearer] = $this->provision('widget-search');
        $this->verifyUser($bearer, 'alice@demo.example.test');
        $this->verifyUser($bearer, 'bob@demo.example.test');
        $this->verifyUser($bearer, '%_literal@demo.example.test');

        [$otherPlatform, , , $otherBearer] = $this->provision('widget-search-other');
        $this->verifyUser($otherBearer, 'alice@demo.example.test');
        $otherIntegration = $otherPlatform->integration;
        $otherKey = $otherIntegration?->primaryApiKey();
        $this->assertNotNull($otherKey);
        // A DIFFERENT platform mints its own widget session for its own alice —
        // its search must never see platform A's users.
        $otherToken = $this->app->make(WidgetSessionService::class)
            ->issue($otherIntegration, $otherKey, 'alice@demo.example.test')->token;

        $widgetToken = $this->mintWidgetSession($bearer, 'alice@demo.example.test');

        $this->getJson('/api/v1/widget/integration/users?q=alice', [
            'Authorization' => 'Bearer '.$widgetToken,
        ])->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.external_user_id', 'alice@demo.example.test');

        // A's BOB is mapped on A but not on B — B's token can never find him.
        $this->getJson('/api/v1/widget/integration/users?q=bob', [
            'Authorization' => 'Bearer '.$otherToken,
        ])->assertOk()
            ->assertJsonCount(0, 'data');

        // `%` is treated literally (escaped LIKE), so the literal-user does not
        // appear for a bare `%` query unless the value itself contains `%`.
        $this->getJson('/api/v1/widget/integration/users?q=%25_literal', [
            'Authorization' => 'Bearer '.$widgetToken,
        ])->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.external_user_id', '%_literal@demo.example.test');

        // A bare `%` query matches ONLY the literal-%-user (the LIKE wildcards in
        // the query are escaped); without escaping it would match every user.
        $this->getJson('/api/v1/widget/integration/users?q=%25', [
            'Authorization' => 'Bearer '.$widgetToken,
        ])->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.external_user_id', '%_literal@demo.example.test');
    }

    public function test_widget_session_can_be_revoked_and_is_then_rejected(): void
    {
        [, , , $bearer] = $this->provision('widget-revoke');
        $this->verifyUser($bearer, 'alice@demo.example.test');
        $widgetToken = $this->mintWidgetSession($bearer, 'alice@demo.example.test');

        $this->postJson('/api/v1/widget/session/revoke', [], [
            'Authorization' => 'Bearer '.$widgetToken,
        ])->assertOk()
            ->assertJsonPath('data.revoked_jti', fn ($jti) => is_string($jti) && $jti !== '');

        $this->getJson('/api/v1/widget/chat/conversations', [
            'Authorization' => 'Bearer '.$widgetToken,
        ])->assertStatus(401)
            ->assertJsonPath('error.code', 'TOKEN_REVOKED');
    }

    public function test_expired_widget_sessions_are_rejected(): void
    {
        [$platform, $integration, , $bearer] = $this->provision('widget-expiry');
        $this->verifyUser($bearer, 'alice@demo.example.test');

        $key = $integration->primaryApiKey();
        $this->assertNotNull($key);

        // Issue with a now from the past so `exp` has already passed.
        $expired = $this->app->make(WidgetSessionService::class)
            ->issue($integration, $key, 'alice@demo.example.test', now()->modify('-30 minutes'));

        $this->getJson('/api/v1/widget/chat/conversations', [
            'Authorization' => 'Bearer '.$expired->token,
        ])->assertStatus(401)
            ->assertJsonPath('error.code', 'TOKEN_EXPIRED');
    }

    public function test_widget_sessions_are_single_platform_isolated(): void
    {
        [, , , $bearerA] = $this->provision('widget-isolate-a');
        $this->verifyUser($bearerA, 'alice@demo.example.test');
        [$platformB, , , $bearerB] = $this->provision('widget-isolate-b');
        $this->verifyUser($bearerB, 'alice@demo.example.test');

        // Platform B opens a thread; platform A's session must not see it.
        [$threadB] = $this->makeConversation($platformB, $bearerB, ['alice@demo.example.test', 'bob@demo.example.test']);
        $this->verifyUser($bearerB, 'bob@demo.example.test');

        $tokenA = $this->mintWidgetSession($bearerA, 'alice@demo.example.test');

        $this->getJson("/api/v1/widget/chat/conversations/{$threadB}", [
            'Authorization' => 'Bearer '.$tokenA,
        ])->assertStatus(404)
            ->assertJsonPath('error.code', 'NOT_FOUND');
    }

    private function mintWidgetSession(string $bearer, string $externalUserId): string
    {
        $response = $this->postJson('/api/v1/widget/session', ['external_user_id' => $externalUserId], [
            'Authorization' => 'Bearer '.$bearer,
        ]);
        $response->assertOk();

        return (string) $response->json('data.access_token');
    }

    private function verifyUser(string $bearer, string $externalUserId): void
    {
        $this->postJson('/api/v1/users/verify', ['external_user_id' => $externalUserId], ['Authorization' => 'Bearer '.$bearer])->assertOk();
    }

    /**
     * @param  array{0: string, 1: string}  $externalIds
     * @return array{0: string}
     */
    private function makeConversation(Platform $platform, string $bearer, array $externalIds): array
    {
        foreach ($externalIds as $id) {
            $this->verifyUser($bearer, $id);
        }

        $response = $this->postJson('/api/v1/chat/conversations', [
            'participant_external_ids' => $externalIds,
        ], ['Authorization' => 'Bearer '.$bearer]);

        $response->assertOk();

        return [(string) $response->json('data.id')];
    }

    /**
     * @return array{0: Platform, 1: PlatformIntegration, 2: mixed, 3: string}
     */
    private function provision(string $slug = 'widgetband'): array
    {
        [$platform, $integration, $secret] = $this->app
            ->make(PlatformOnboardingService::class)
            ->onboard($slug);

        $this->assertNotNull($secret);
        $response = $this->postJson('/api/v1/auth/token', [
            'client_id' => $platform->public_id,
            'client_secret' => (string) $secret,
        ]);
        $response->assertOk();

        return [$platform, $integration, $secret, (string) $response->json('data.access_token')];
    }

    /**
     * @param  list<string>  $scopes
     */
    private function mintPlatformToken(Platform $platform, PlatformIntegration $integration, array $scopes): string
    {
        $key = $integration->primaryApiKey();
        $this->assertNotNull($key);

        return $this->app->make(PasetoTokenService::class)->issue($integration, $key, $scopes)->token;
    }
}
