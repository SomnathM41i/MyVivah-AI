<?php

namespace Tests\Feature;

use App\Models\Platform;
use App\Models\PlatformIntegration;
use App\Services\PlatformOnboardingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Broadcast;
use Tests\Support\RecordingBroadcaster;
use Tests\TestCase;

/**
 * Phase 4 widget END-TO-END flows: thread open/send/read over the widget API,
 * actor-must-be-participant enforcement, presence + socket auth identity,
 * realtime broadcast parity, and per-origin CORS restriction.
 */
class WidgetIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        RecordingBroadcaster::reset();
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_widget_can_open_send_and_read_a_thread_end_to_end(): void
    {
        [$platform, , , $bearer] = $this->provision('widget-e2e');
        $this->verifyUser($bearer, 'alice@demo.example.test');
        $this->verifyUser($bearer, 'bob@demo.example.test');
        [$thread] = $this->makeConversation($platform, $bearer, ['alice@demo.example.test', 'bob@demo.example.test']);

        $aliceWidget = $this->mintWidgetSession($bearer, 'alice@demo.example.test');

        // Browsers never send X-External-User-Id — identity is the token.
        $headers = ['Authorization' => 'Bearer '.$aliceWidget];

        $this->postJson("/api/v1/widget/chat/conversations/{$thread}/messages", [
            'client_message_id' => 'widget-msg-01',
            'content' => 'hello from the widget',
        ], $headers)->assertStatus(201)
            ->assertJsonPath('data.content', 'hello from the widget')
            ->assertJsonPath('meta.duplicate', false);

        // Idempotent re-send (network retry) returns the SAME message.
        $this->postJson("/api/v1/widget/chat/conversations/{$thread}/messages", [
            'client_message_id' => 'widget-msg-01',
            'content' => 'hello from the widget',
        ], $headers)->assertStatus(201)
            ->assertJsonPath('meta.duplicate', true);

        $history = $this->getJson("/api/v1/widget/chat/conversations/{$thread}/messages", $headers)
            ->assertOk();
        $this->assertCount(1, $history->json('data'));
        $this->assertSame('alice@demo.example.test', $history->json('data.0.sender.external_user_id'));

        $this->getJson('/api/v1/widget/chat/conversations', $headers)
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $thread);
    }

    public function test_widget_cannot_open_a_thread_for_other_users(): void
    {
        [, , , $bearer] = $this->provision('widget-guard');
        $this->verifyUser($bearer, 'alice@demo.example.test');
        $this->verifyUser($bearer, 'bob@demo.example.test');
        $this->verifyUser($bearer, 'carol@demo.example.test');

        $aliceWidget = $this->mintWidgetSession($bearer, 'alice@demo.example.test');

        $this->postJson('/api/v1/widget/chat/conversations', [
            'participant_external_ids' => ['bob@demo.example.test', 'carol@demo.example.test'],
        ], ['Authorization' => 'Bearer '.$aliceWidget])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_FAILED');
    }

    public function test_widget_unread_mark_read_flow(): void
    {
        [$platform, , , $bearer] = $this->provision('widget-unread');
        $this->verifyUser($bearer, 'alice@demo.example.test');
        $this->verifyUser($bearer, 'bob@demo.example.test');
        [$thread] = $this->makeConversation($platform, $bearer, ['alice@demo.example.test', 'bob@demo.example.test']);

        // alice writes two messages (platform-side).
        foreach (['u1', 'u2'] as $i) {
            $this->postJson("/api/v1/chat/conversations/{$thread}/messages", [
                'client_message_id' => "widget-unread-{$i}",
                'content' => "msg {$i}",
            ], ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'alice@demo.example.test'])
                ->assertStatus(201);
        }

        $bobWidget = $this->mintWidgetSession($bearer, 'bob@demo.example.test');
        $bobHeaders = ['Authorization' => 'Bearer '.$bobWidget];

        $this->getJson('/api/v1/widget/chat/conversations', $bobHeaders)
            ->assertOk()
            ->assertJsonPath('data.0.unread_count', 2);

        $this->postJson("/api/v1/widget/chat/conversations/{$thread}/read", [], $bobHeaders)
            ->assertOk()
            ->assertJsonPath('data.unread_count', 0);

        $this->getJson('/api/v1/widget/chat/conversations', $bobHeaders)
            ->assertOk()
            ->assertJsonPath('data.0.unread_count', 0);
    }

    public function test_widget_presence_and_socket_auth_use_token_identity(): void
    {
        $this->enableRealtime();
        [$platform, , , $bearer] = $this->provision('widget-presence');
        $this->verifyUser($bearer, 'alice@demo.example.test');
        $this->verifyUser($bearer, 'bob@demo.example.test');
        [$thread] = $this->makeConversation($platform, $bearer, ['alice@demo.example.test', 'bob@demo.example.test']);

        $aliceWidget = $this->mintWidgetSession($bearer, 'alice@demo.example.test');
        $bobWidget = $this->mintWidgetSession($bearer, 'bob@demo.example.test');

        $this->postJson('/api/v1/widget/chat/presence', ['status' => 'online'], [
            'Authorization' => 'Bearer '.$aliceWidget,
        ])->assertOk();

        $this->getJson('/api/v1/widget/chat/presence/me', [
            'Authorization' => 'Bearer '.$aliceWidget,
        ])->assertOk()
            ->assertJsonPath('data.presence_status', 'online');

        // alice (a participant) authorizes the private thread channel.
        $this->postJson('/api/v1/widget/chat/socket/auth', [
            'socket_id' => '123.456',
            'channel_name' => "private-chat.{$thread}",
        ], ['Authorization' => 'Bearer '.$aliceWidget])
            ->assertOk()
            ->assertJsonPath('data.authorized', true);

        // A member channel for a thread alice is NOT in must be denied — and a
        // SPOOFED X-External-User-Id (a participant's id) must NOT grant it.
        $this->verifyUser($bearer, 'carol@demo.example.test');
        [$threadC] = $this->makeConversation($platform, $bearer, ['alice@demo.example.test', 'carol@demo.example.test']);

        $this->postJson('/api/v1/widget/chat/socket/auth', [
            'socket_id' => '123.456',
            'channel_name' => "private-chat.{$threadC}",
        ], [
            'Authorization' => 'Bearer '.$bobWidget,
            'X-External-User-Id' => 'alice@demo.example.test', // spoofed participant
        ])->assertStatus(403)
            ->assertJsonPath('error.code', 'CHANNEL_DENIED');
    }

    public function test_widget_message_send_broadcasts_like_the_platform_flow(): void
    {
        $this->enableRealtime();
        [$platform, , , $bearer] = $this->provision('widget-broadcast');
        $this->verifyUser($bearer, 'alice@demo.example.test');
        $this->verifyUser($bearer, 'bob@demo.example.test');
        [$thread] = $this->makeConversation($platform, $bearer, ['alice@demo.example.test', 'bob@demo.example.test']);

        $aliceWidget = $this->mintWidgetSession($bearer, 'alice@demo.example.test');

        $this->postJson("/api/v1/widget/chat/conversations/{$thread}/messages", [
            'client_message_id' => 'widget-rt-01',
            'content' => 'realtime from the widget',
        ], ['Authorization' => 'Bearer '.$aliceWidget])->assertStatus(201);

        $created = $this->emitted('message.created');
        $this->assertCount(1, $created);
        $this->assertSame("private-chat.{$thread}", $created[0]['channels'][0]);
        $this->assertSame('alice@demo.example.test', $created[0]['payload']['sender_id']);
    }

    public function test_widget_cors_preflight_is_served_only_for_widget_paths(): void
    {
        $this->withHeaders(['Origin' => 'https://embedding.example', 'Access-Control-Request-Method' => 'POST'])
            ->options('/api/v1/widget/chat/conversations')
            ->assertNoContent(204)
            ->assertHeader('Access-Control-Allow-Origin', '*');

        $this->withHeaders(['Origin' => 'https://embedding.example', 'Access-Control-Request-Method' => 'POST'])
            ->options('/api/v1/chat/conversations')
            ->assertStatus(200)
            ->assertHeaderMissing('Access-Control-Allow-Origin');
    }

    public function test_restrict_widget_origins_strips_allow_origin_for_unknown_origins(): void
    {
        [$platform, , , $bearer] = $this->provision('widget-origin');
        $this->verifyUser($bearer, 'alice@demo.example.test');

        $integration = $platform->integration;
        $this->assertNotNull($integration);
        $integration->forceFill(['allowed_origins' => ['https://allowed.example', 'https://*.sub.example']])->save();

        $widgetToken = $this->mintWidgetSession($bearer, 'alice@demo.example.test');

        // Server-to-server / non-browser call (no Origin): no CORS header at all.
        $this->getJson('/api/v1/widget/integration/users', [
            'Authorization' => 'Bearer '.$widgetToken,
        ])->assertOk()
            ->assertHeaderMissing('Access-Control-Allow-Origin');

        // Exact allowed origin keeps the ACAO header.
        $this->getJson('/api/v1/widget/integration/users', [
            'Authorization' => 'Bearer '.$widgetToken,
            'Origin' => 'https://allowed.example',
        ])->assertOk()
            ->assertHeader('Access-Control-Allow-Origin', '*');

        // A subdomain allowed by the `*.` pattern keeps it too.
        $this->getJson('/api/v1/widget/integration/users', [
            'Authorization' => 'Bearer '.$widgetToken,
            'Origin' => 'https://app.sub.example',
        ])->assertOk()
            ->assertHeader('Access-Control-Allow-Origin', '*');

        // Unknown origin → header stripped so the browser will not expose data.
        $this->getJson('/api/v1/widget/integration/users', [
            'Authorization' => 'Bearer '.$widgetToken,
            'Origin' => 'https://evil.example',
        ])->assertOk()
            ->assertHeaderMissing('Access-Control-Allow-Origin');
    }

    public function test_demo_page_only_exists_locally(): void
    {
        $this->get('/demo/chat')->assertStatus(404);

        config(['app.env' => 'local', 'app.debug' => true]);
        $this->get('/demo/chat')->assertOk();
    }

    private function enableRealtime(): void
    {
        RecordingBroadcaster::reset();
        config([
            'chat.realtime.enabled' => true,
            'chat.realtime.app_key' => 'widget_test_app_key',
            'chat.realtime.app_secret' => 'widget_test_app_secret_branch_secret_x',
            'chat.realtime.presence_offline_after_seconds' => 60,
            'chat.realtime.presence_sweep_idle_after_seconds' => 30,
            'chat.realtime.presence_sweep_limit' => 500,
            'broadcasting.default' => 'recording',
            'broadcasting.connections.recording' => ['driver' => 'recording'],
        ]);
        Broadcast::extend('recording', static fn (): RecordingBroadcaster => new RecordingBroadcaster);
        Broadcast::forgetDrivers();
    }

    /**
     * @return array<int, array{channels: array<int, string>, event: string, payload: array<string, mixed>}>
     */
    private function emitted(string $event): array
    {
        return array_values(array_filter(
            RecordingBroadcaster::$broadcasts,
            static fn (array $broadcast): bool => $broadcast['event'] === $event,
        ));
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
}
