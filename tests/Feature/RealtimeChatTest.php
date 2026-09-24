<?php

namespace Tests\Feature;

use App\Models\ExternalUserMap;
use App\Models\Message;
use App\Models\Platform;
use App\Models\PlatformIntegration;
use App\Services\PasetoTokenService;
use App\Services\PlatformOnboardingService;
use App\Services\PresenceService;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Broadcast;
use Tests\Support\RecordingBroadcaster;
use Tests\TestCase;

/**
 * Phase 3E realtime: broadcast + presence + channel authorization, with
 * Redis-optional, REST-always semantics.
 *
 * Realtime-ON tests record dispatch through a process-memory broadcaster so we
 * assert WHAT fired, WHEN (persist-before-broadcast) and the exact
 * Pusher-protocol shape. The transport-dead tests prove the REST surface keeps
 * working even when the realtime driver throws — the guarantee that lets shared
 * hosting ship Phase 3D today and Phase 3E when infra exists.
 */
class RealtimeChatTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        RecordingBroadcaster::reset();
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_send_persists_the_message_before_any_broadcast(): void
    {
        $this->enableRealtime();
        [$platform, , , $bearer] = $this->provision('rt-persist');
        [$conversation] = $this->makeConversation($platform, $bearer, ['u1', 'u2']);

        RecordingBroadcaster::$onBroadcast = function (array $channels, string $event, array $payload) use ($conversation): void {
            if ($event !== 'message.created') {
                return;
            }

            $this->assertSame(["private-chat.{$conversation}"], array_map('strval', $channels));
            $row = Message::query()->where('public_id', $payload['message_id'])->first();
            $this->assertNotNull($row, 'the message row must already be committed when the broadcast fires');
            $this->assertSame('already-committed', $row->content);
        };

        $this->postJson(
            "/api/v1/chat/conversations/{$conversation}/messages",
            ['client_message_id' => 'msg-persist-01', 'content' => 'already-committed'],
            ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u1'],
        )->assertStatus(201)
            ->assertJsonPath('meta.duplicate', false);

        $this->assertCount(1, $this->emitted('message.created'));
    }

    public function test_message_created_broadcasts_minimal_payload_with_no_secrets(): void
    {
        $this->enableRealtime();
        [$platform, , , $bearer] = $this->provision('rt-payload');
        [$conversation] = $this->makeConversation($platform, $bearer, ['u1', 'u2']);

        $this->postJson(
            "/api/v1/chat/conversations/{$conversation}/messages",
            ['client_message_id' => 'msg-payload-01', 'content' => 'hello realtime'],
            ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u1'],
        )->assertStatus(201);

        $created = $this->emitted('message.created');
        $this->assertCount(1, $created, 'broadcasts='.json_encode(RecordingBroadcaster::$broadcasts));
        $payload = $created[0]['payload'];

        foreach (['conversation_id', 'message_id', 'sender_id', 'type', 'status', 'content', 'client_message_id', 'sent_at'] as $key) {
            $this->assertArrayHasKey($key, $payload);
        }
        $this->assertSame($conversation, $payload['conversation_id']);
        $this->assertSame('u1', $payload['sender_id']);
        $this->assertSame('text', $payload['type']);
        $this->assertSame('sent', $payload['status']);
        $this->assertSame('hello realtime', $payload['content']);
        $this->assertNotContains('app_secret', array_keys($payload));
        $this->assertNotContains('app_key', array_keys($payload));

        $list = $this->emitted('conversation.updated');
        $this->assertCount(2, $list);
        $this->assertSame('created', $list[0]['payload']['reason']);
        $this->assertNull($list[0]['payload']['last_message']);
        $this->assertSame('updated', $list[1]['payload']['reason']);
        $this->assertSame(['u1', 'u2'], $list[1]['payload']['participants']);
        $this->assertSame('hello realtime', $list[1]['payload']['last_message']['excerpt']);
        $this->assertSame('u1', $list[1]['payload']['last_message']['sender_id']);
    }

    public function test_duplicate_send_never_rebroadcasts(): void
    {
        $this->enableRealtime();
        [$platform, , , $bearer] = $this->provision('rt-dedup');
        [$conversation] = $this->makeConversation($platform, $bearer, ['u1', 'u2']);

        $payload = ['client_message_id' => 'msg-dedup-01', 'content' => 'once'];
        $headers = ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u1'];

        $this->postJson("/api/v1/chat/conversations/{$conversation}/messages", $payload, $headers)->assertStatus(201);
        $this->postJson("/api/v1/chat/conversations/{$conversation}/messages", $payload, $headers)
            ->assertStatus(201)
            ->assertJsonPath('meta.duplicate', true);

        $this->assertCount(1, $this->emitted('message.created'));
        $this->assertCount(2, $this->emitted('conversation.updated'));
    }

    public function test_new_conversation_broadcasts_created_and_send_broadcasts_updated(): void
    {
        $this->enableRealtime();
        [$platform, , , $bearer] = $this->provision('rt-created');

        foreach (['c1', 'c2'] as $id) {
            $this->postJson('/api/v1/users/verify', ['external_user_id' => $id], ['Authorization' => 'Bearer '.$bearer])->assertOk();
        }

        $response = $this->postJson('/api/v1/chat/conversations', [
            'participant_external_ids' => ['c1', 'c2'],
        ], ['Authorization' => 'Bearer '.$bearer]);

        $response->assertOk()->assertJsonPath('meta.created', true);
        $conversationId = (string) $response->json('data.id');

        $created = $this->emitted('conversation.updated');
        $this->assertCount(1, $created);
        $this->assertSame('created', $created[0]['payload']['reason']);
        $this->assertSame(["private-chat.{$conversationId}"], $created[0]['channels']);
        $this->assertSame(['c1', 'c2'], $created[0]['payload']['participants']);
        $this->assertNull($created[0]['payload']['last_message']);

        $this->postJson(
            "/api/v1/chat/conversations/{$conversationId}/messages",
            ['client_message_id' => 'msg-created-01', 'content' => 'first'],
            ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'c1'],
        )->assertStatus(201);

        $updated = $this->emitted('conversation.updated');
        $this->assertCount(2, $updated);
        $this->assertSame('updated', $updated[1]['payload']['reason']);
    }

    public function test_mark_read_broadcasts_message_read_with_advance_only_cursor(): void
    {
        $this->enableRealtime();
        [$platform, , , $bearer] = $this->provision('rt-read');
        [$conversation] = $this->makeConversation($platform, $bearer, ['u1', 'u2']);

        $messagePublicId = (string) $this->postJson(
            "/api/v1/chat/conversations/{$conversation}/messages",
            ['client_message_id' => 'msg-read-01', 'content' => 'read me'],
            ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u1'],
        )->assertStatus(201)->json('data.id');

        $internalMessageId = $this->internalMessageId($messagePublicId);
        $this->assertIsInt($internalMessageId);

        $this->postJson(
            "/api/v1/chat/conversations/{$conversation}/read",
            ['last_read_message_id' => $internalMessageId],
            ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u2'],
        )->assertOk();

        $read = $this->emitted('message.read');
        $this->assertCount(1, $read);
        $this->assertSame(["private-chat.{$conversation}"], $read[0]['channels']);
        $payload = $read[0]['payload'];
        $this->assertSame('u2', $payload['reader_id']);
        $this->assertSame($internalMessageId, $payload['last_read_message_id']);
        $this->assertSame(0, $payload['unread_count']);
        $this->assertIsString($payload['read_at']);
    }

    public function test_presence_heartbeat_broadcasts_only_on_transition(): void
    {
        $this->enableRealtime();
        [$platform, , , $bearer] = $this->provision('rt-pres-transition');
        $this->verifyUser($bearer, 'p1');
        $presenceChannel = "presence-chat.{$platform->public_id}";

        $online = $this->postJson('/api/v1/chat/presence', [], ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'p1']);
        $online->assertOk()
            ->assertJsonPath('data.presence_status', 'online')
            ->assertJsonPath('data.changed', true)
            ->assertJsonPath('meta.realtime_enabled', true);

        $this->assertSame(1, $this->onlineCount($platform));
        $userOnline = $this->emitted('user.online');
        $this->assertSame([$presenceChannel], $userOnline[0]['channels']);
        $this->assertSame('p1', $userOnline[0]['payload']['external_user_id']);
        $this->assertSame('online', $userOnline[0]['payload']['status']);
        $this->assertArrayHasKey('seen_at', $userOnline[0]['payload']);

        $repeat = $this->postJson('/api/v1/chat/presence', [], ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'p1']);
        $repeat->assertOk()->assertJsonPath('data.changed', false);
        $this->assertSame(1, $this->onlineCount($platform));

        $offline = $this->postJson('/api/v1/chat/presence', ['status' => 'offline'], ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'p1']);
        $offline->assertOk()
            ->assertJsonPath('data.presence_status', 'offline')
            ->assertJsonPath('data.changed', true);

        $offlineEvents = $this->emitted('user.offline');
        $this->assertCount(1, $offlineEvents);
        $this->assertSame([$presenceChannel], $offlineEvents[0]['channels']);
        $this->assertSame('p1', $offlineEvents[0]['payload']['external_user_id']);
        $this->assertSame('offline', $offlineEvents[0]['payload']['status']);

        $again = $this->postJson('/api/v1/chat/presence', ['status' => 'offline'], ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'p1']);
        $again->assertOk()->assertJsonPath('data.changed', false);
        $this->assertCount(1, $this->emitted('user.offline'));
    }

    public function test_presence_sweep_flips_stale_online_and_lets_a_reconnect_announce_again(): void
    {
        $this->enableRealtime();
        Carbon::setTestNow('2026-01-01 10:00:00');

        [$platform, , , $bearer] = $this->provision('rt-sweep');
        $this->verifyUser($bearer, 's1');

        $this->postJson('/api/v1/chat/presence', [], ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 's1'])
            ->assertOk()->assertJsonPath('data.presence_status', 'online');
        $this->assertSame(1, $this->onlineCount($platform));

        Carbon::setTestNow('2026-01-01 10:05:00');

        $flipped = $this->app->make(PresenceService::class)->sweep($platform);
        $this->assertSame(1, $flipped);

        $offlineEvents = $this->emitted('user.offline');
        $this->assertCount(1, $offlineEvents);
        $this->assertSame('s1', $offlineEvents[0]['payload']['external_user_id']);

        $this->getJson('/api/v1/chat/presence/s1', ['Authorization' => 'Bearer '.$bearer])
            ->assertOk()
            ->assertJsonPath('data.presence_status', 'offline');

        Carbon::setTestNow('2026-01-01 11:00:00');

        $reconnect = $this->postJson('/api/v1/chat/presence', [], ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 's1']);
        $reconnect->assertOk()->assertJsonPath('data.changed', true);
        $this->assertSame(2, $this->onlineCount($platform));

        $this->artisan('chat:presence-sweep')->assertExitCode(0);
    }

    public function test_private_channel_auth_returns_valid_pusher_protocol_signature(): void
    {
        $this->enableRealtime();
        [$platform, , , $bearer] = $this->provision('rt-auth-ok');
        [$conversation] = $this->makeConversation($platform, $bearer, ['u1', 'u2']);

        $channel = "private-chat.{$conversation}";
        $socketId = '1234.5678';

        $response = $this->postJson('/api/v1/chat/socket/auth', [
            'socket_id' => $socketId,
            'channel_name' => $channel,
        ], ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u1']);

        $response->assertOk()->assertJsonPath('data.authorized', true);
        $this->assertSame($this->pusherSignature($socketId, $channel), $response->json('data.auth'));
        $this->assertNull($response->json('data.channel_data'));
    }

    public function test_private_channel_auth_denies_non_participant_and_cross_platform(): void
    {
        $this->enableRealtime();
        [$platform, , , $bearer] = $this->provision('rt-auth-deny');
        [$conversation] = $this->makeConversation($platform, $bearer, ['u1', 'u2']);
        $this->verifyUser($bearer, 'u3');
        [, , , $bearerB] = $this->provision('rt-auth-deny-b');
        $this->verifyUser($bearerB, 'b1');

        $channel = "private-chat.{$conversation}";

        $this->postJson('/api/v1/chat/socket/auth', ['socket_id' => '1234.5678', 'channel_name' => $channel], ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u3'])
            ->assertStatus(403)
            ->assertJsonPath('error.code', 'CHANNEL_DENIED');

        $this->postJson('/api/v1/chat/socket/auth', ['socket_id' => '1234.5678', 'channel_name' => $channel], ['Authorization' => 'Bearer '.$bearerB, 'X-External-User-Id' => 'b1'])
            ->assertStatus(403)
            ->assertJsonPath('error.code', 'CHANNEL_DENIED');
    }

    public function test_presence_channel_auth_allows_platform_members_and_denies_foreign_platforms(): void
    {
        $this->enableRealtime();
        [$platform, , , $bearer] = $this->provision('rt-presence-auth');
        $this->verifyUser($bearer, 'pa1');
        $this->verifyUser($bearer, 'pa2');
        [, , , $bearerB] = $this->provision('rt-presence-auth-b');
        $this->verifyUser($bearerB, 'pb1');

        $channel = "presence-chat.{$platform->public_id}";
        $socketId = '9876.5432';
        $channelData = '{"user_id":"'.$this->externalUserMapId($platform, 'pa1').'","user_info":{"external_user_id":"pa1","presence_status":"offline"}}';

        $allowed = $this->postJson('/api/v1/chat/socket/auth', [
            'socket_id' => $socketId,
            'channel_name' => $channel,
        ], ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'pa1']);

        $allowed->assertOk()->assertJsonPath('data.authorized', true);
        $this->assertSame($this->pusherSignature($socketId, $channel, $channelData), $allowed->json('data.auth'));
        $this->assertSame($channelData, $allowed->json('data.channel_data'));

        $this->postJson('/api/v1/chat/socket/auth', [
            'socket_id' => $socketId,
            'channel_name' => $channel,
        ], ['Authorization' => 'Bearer '.$bearerB, 'X-External-User-Id' => 'pb1'])
            ->assertStatus(403)
            ->assertJsonPath('error.code', 'CHANNEL_DENIED');
    }

    public function test_unknown_channel_and_invalid_socket_id_are_rejected(): void
    {
        $this->enableRealtime();
        [$platform, , , $bearer] = $this->provision('rt-auth-unknown');
        $this->verifyUser($bearer, 'x1');

        $this->postJson('/api/v1/chat/socket/auth', ['socket_id' => '1234.5678', 'channel_name' => 'not-a-known-channel'], ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'x1'])
            ->assertStatus(403)
            ->assertJsonPath('error.code', 'CHANNEL_DENIED');

        $this->postJson('/api/v1/chat/socket/auth', ['socket_id' => 'nope', 'channel_name' => "presence-chat.{$platform->public_id}"], ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'x1'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('socket_id', 'error.errors');
    }

    public function test_socket_auth_and_heartbeat_enforce_scopes(): void
    {
        $this->enableRealtime();
        [$platform, $integration, , $bearer] = $this->provision('rt-scopes');
        $this->verifyUser($bearer, 'sc1');

        $readOnly = $this->mintToken($platform, $integration, ['realtime_chat:read']);
        $writeOnly = $this->mintToken($platform, $integration, ['realtime_chat:write']);

        // heartbeat is a write → read-only token is denied
        $this->postJson('/api/v1/chat/presence', [], ['Authorization' => 'Bearer '.$readOnly, 'X-External-User-Id' => 'sc1'])
            ->assertStatus(403)
            ->assertJsonPath('error.code', 'SERVICE_NO_ACCESS');

        // socket auth is a read → write-only token is denied
        $this->postJson('/api/v1/chat/socket/auth', ['socket_id' => '1234.5678', 'channel_name' => 'private-chat.anything'], ['Authorization' => 'Bearer '.$writeOnly, 'X-External-User-Id' => 'sc3'])
            ->assertStatus(403)
            ->assertJsonPath('error.code', 'SERVICE_NO_ACCESS');
    }

    public function test_realtime_disabled_rest_only_stack_stays_quiet_and_working(): void
    {
        [$platform, , , $bearer] = $this->provision('rt-disabled');
        [$conversation] = $this->makeConversation($platform, $bearer, ['u1', 'u2']);

        config(['chat.realtime.enabled' => false]);

        $this->postJson(
            "/api/v1/chat/conversations/{$conversation}/messages",
            ['client_message_id' => 'msg-quiet-01', 'content' => 'quiet'],
            ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u1'],
        )->assertStatus(201);

        $this->getJson("/api/v1/chat/conversations/{$conversation}/messages", ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u1'])
            ->assertOk()
            ->assertJsonPath('data.0.content', 'quiet');

        $this->postJson('/api/v1/chat/presence', ['status' => 'online'], ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u1'])
            ->assertOk()->assertJsonPath('meta.realtime_enabled', false);

        $this->assertSame([], RecordingBroadcaster::$broadcasts);
    }

    public function test_dead_realtime_transport_never_breaks_rest(): void
    {
        $this->enableRealtime();
        RecordingBroadcaster::$throw = new BroadcastException('connection refused to realtime server');

        [$platform, , , $bearer] = $this->provision('rt-transport');
        [$conversation] = $this->makeConversation($platform, $bearer, ['u1', 'u2']);

        // the conversation.updated (created) broadcast above already tripped a
        // dead transport and was rescued; the REST surface must stay usable
        $this->postJson(
            "/api/v1/chat/conversations/{$conversation}/messages",
            ['client_message_id' => 'msg-transport-01', 'content' => 'still delivered'],
            ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u1'],
        )->assertStatus(201)
            ->assertJsonPath('data.content', 'still delivered');

        $this->postJson(
            "/api/v1/chat/conversations/{$conversation}/messages",
            ['client_message_id' => 'msg-transport-01', 'content' => 'still delivered'],
            ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u1'],
        )->assertStatus(201)
            ->assertJsonPath('meta.duplicate', true);

        $this->postJson('/api/v1/chat/presence', [], ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u1'])
            ->assertOk()
            ->assertJsonPath('data.presence_status', 'online');

        $this->getJson("/api/v1/chat/conversations/{$conversation}/messages", ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u1'])
            ->assertOk()
            ->assertJsonPath('data.0.content', 'still delivered');

        // nothing was actually pushed anywhere
        $this->assertSame([], RecordingBroadcaster::$broadcasts);
    }

    public function test_presence_show_works_as_polling_fallback_and_404s_unknown_ids(): void
    {
        $this->enableRealtime();
        [$platform, , , $bearer] = $this->provision('rt-poll');
        [, , , $bearerB] = $this->provision('rt-poll-b');
        $this->verifyUser($bearer, 'p2');
        $this->verifyUser($bearerB, 'pb2');

        $this->postJson('/api/v1/chat/presence', [], ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'p2'])
            ->assertOk();

        $this->getJson('/api/v1/chat/presence/p2', ['Authorization' => 'Bearer '.$bearer])
            ->assertOk()
            ->assertJsonPath('data.presence_status', 'online');

        $this->getJson('/api/v1/chat/presence/does-not-exist', ['Authorization' => 'Bearer '.$bearer])
            ->assertStatus(404)
            ->assertJsonPath('error.code', 'NOT_FOUND');

        $this->getJson('/api/v1/chat/presence/pb2', ['Authorization' => 'Bearer '.$bearer])
            ->assertStatus(404)
            ->assertJsonPath('error.code', 'NOT_FOUND');
    }

    private function enableRealtime(): void
    {
        RecordingBroadcaster::reset();
        config([
            'chat.realtime.enabled' => true,
            'chat.realtime.app_key' => 'rt_test_app_key',
            'chat.realtime.app_secret' => 'rt_test_app_secret_branch_secret_x',
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

    private function onlineCount(Platform $platform): int
    {
        return count(array_filter(
            RecordingBroadcaster::$broadcasts,
            static fn (array $broadcast): bool => $broadcast['event'] === 'user.online'
                && in_array("presence-chat.{$platform->public_id}", $broadcast['channels'], true),
        ));
    }

    private function pusherSignature(string $socketId, string $channel, ?string $channelData = null): string
    {
        $key = (string) config('chat.realtime.app_key');
        $secret = (string) config('chat.realtime.app_secret');
        $signingString = $channelData !== null
            ? $socketId.':'.$channel.':'.$channelData
            : $socketId.':'.$channel;

        return $key.':'.hash_hmac('sha256', $signingString, $secret);
    }

    private function verifyUser(string $bearer, string $externalUserId): void
    {
        $this->postJson('/api/v1/users/verify', ['external_user_id' => $externalUserId], ['Authorization' => 'Bearer '.$bearer])->assertOk();
    }

    /**
     * @return array{0: Platform, 1: PlatformIntegration, 2: mixed, 3: string}
     */
    private function provision(string $slug = 'rtband'): array
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

    private function internalMessageId(string $publicMessageId): ?int
    {
        $id = Message::query()->where('public_id', $publicMessageId)->value('id');

        return $id === null ? null : (int) $id;
    }

    private function externalUserMapId(Platform $platform, string $externalUserId): int
    {
        return (int) ExternalUserMap::query()
            ->where('platform_id', $platform->id)
            ->where('external_user_id', $externalUserId)
            ->value('id');
    }

    /**
     * @param  list<string>  $scopes
     */
    private function mintToken(Platform $platform, PlatformIntegration $integration, array $scopes): string
    {
        $key = $integration->primaryApiKey();
        $this->assertNotNull($key);

        return $this->app->make(PasetoTokenService::class)->issue($integration, $key, $scopes)->token;
    }
}
