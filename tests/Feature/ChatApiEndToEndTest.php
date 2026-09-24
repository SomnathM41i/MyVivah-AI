<?php

namespace Tests\Feature;

use App\Models\ApiAuditLog;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Platform;
use App\Models\PlatformIntegration;
use App\Services\PasetoTokenService;
use App\Services\PlatformOnboardingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Phase 3D end-to-end: the chat API foundation.
 *
 * Exercises resolve-or-create determinism, platform+actor scoping/isolation,
 * idempotent sends, cursor pagination, per-user unread/read state (advance-only),
 * scope enforcement and audit coverage across /api/v1/chat — under the standard
 * envelope and the same authenticated stack as the rest of the platform surface.
 */
class ChatApiEndToEndTest extends TestCase
{
    use RefreshDatabase;

    public function test_conversation_create_resolves_deterministically_for_a_pair(): void
    {
        [$platform, , , $bearer] = $this->provision('plat-a');

        $this->postJson('/api/v1/users/verify', ['external_user_id' => 'ext-a'], ['Authorization' => 'Bearer '.$bearer])->assertOk();
        $this->postJson('/api/v1/users/verify', ['external_user_id' => 'ext-b'], ['Authorization' => 'Bearer '.$bearer])->assertOk();

        // reversed argument order — same pair, must resolve to the SAME thread
        $first = $this->postJson('/api/v1/chat/conversations', [
            'participant_external_ids' => ['ext-b', 'ext-a'],
        ], ['Authorization' => 'Bearer '.$bearer]);

        $first->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('meta.created', true)
            ->assertJsonCount(2, 'data.participants');

        $conversationId = $first->json('data.id');
        $this->assertIsString($conversationId);

        $second = $this->postJson('/api/v1/chat/conversations', [
            'participant_external_ids' => ['ext-a', 'ext-b'],
        ], ['Authorization' => 'Bearer '.$bearer]);

        $second->assertOk()
            ->assertJsonPath('data.id', $conversationId)
            ->assertJsonPath('meta.created', false)
            ->assertJsonCount(2, 'data.participants');

        $this->assertSame(1, Conversation::query()
            ->where('platform_id', $platform->id)
            ->count());
    }

    public function test_conversation_create_validates_participant_payload(): void
    {
        [, , , $bearer] = $this->provision('plat-a');

        $this->postJson('/api/v1/chat/conversations', [
            'participant_external_ids' => ['ext-a'],
        ], ['Authorization' => 'Bearer '.$bearer])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_FAILED')
            ->assertJsonValidationErrors('participant_external_ids', 'error.errors');

        $this->postJson('/api/v1/chat/conversations', [
            'participant_external_ids' => ['ext-a', 'ext-a'],
        ], ['Authorization' => 'Bearer '.$bearer])
            ->assertStatus(422)
            ->assertJsonValidationErrors('participant_external_ids', 'error.errors');

        $this->postJson('/api/v1/chat/conversations', [
            'participant_external_ids' => ['bad/path', 'ext-b'],
        ], ['Authorization' => 'Bearer '.$bearer])
            ->assertStatus(422)
            ->assertJsonValidationErrors('participant_external_ids.0', 'error.errors');
    }

    public function test_send_message_is_idempotent_and_unread_is_increment_only_for_others(): void
    {
        [$platform, , , $bearer] = $this->provision('plat-a');
        [$conversationPublicId] = $this->makeConversation($platform, $bearer, ['u1', 'u2']);
        $conversationId = $this->internalConversationId($conversationPublicId);

        $payload = [
            'client_message_id' => 'msg-client-0001',
            'content' => 'hello world',
        ];

        $first = $this->postJson(
            "/api/v1/chat/conversations/{$conversationPublicId}/messages",
            $payload,
            ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u1'],
        );

        $first->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('meta.duplicate', false)
            ->assertJsonPath('data.content', 'hello world');

        $messageId = $first->json('data.id');

        $retry = $this->postJson(
            "/api/v1/chat/conversations/{$conversationPublicId}/messages",
            $payload,
            ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u1'],
        );

        $retry->assertStatus(201)
            ->assertJsonPath('data.id', $messageId)
            ->assertJsonPath('meta.duplicate', true);

        $this->assertSame(1, Message::query()
            ->where('platform_id', $platform->id)
            ->where('conversation_id', $conversationId)
            ->where('client_message_id', 'msg-client-0001')
            ->count());

        // sender's own unread stays 0; the OTHER participant sees 1
        $senderView = $this->getJson('/api/v1/chat/conversations', ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u1']);
        $senderView->assertOk()
            ->assertJsonPath('data.0.unread_count', 0)
            ->assertJsonPath('data.0.last_message.excerpt', 'hello world')
            ->assertJsonPath('data.0.last_message.sender_id', 'u1');

        $peerView = $this->getJson('/api/v1/chat/conversations', ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u2']);
        $peerView->assertOk()
            ->assertJsonPath('data.0.unread_count', 1);
    }

    public function test_message_conversation_authz_and_cross_platform_isolation(): void
    {
        [$platform, , , $bearer] = $this->provision('plat-a');
        [, , , $bearerB] = $this->provision('plat-b');

        [$conversation] = $this->makeConversation($platform, $bearer, ['u1', 'u2']);

        // u3 is mapped on the SAME platform but is NOT a participant
        $this->postJson('/api/v1/users/verify', ['external_user_id' => 'u3'], ['Authorization' => 'Bearer '.$bearer])->assertOk();
        $this->postJson('/api/v1/users/verify', ['external_user_id' => 'b1'], ['Authorization' => 'Bearer '.$bearerB])->assertOk();

        $send = ['client_message_id' => 'msg-client-0009', 'content' => 'sneak'];

        $this->postJson("/api/v1/chat/conversations/{$conversation}/messages", $send, ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u3'])
            ->assertStatus(404)
            ->assertJsonPath('error.code', 'NOT_FOUND');

        $this->getJson("/api/v1/chat/conversations/{$conversation}", ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u3'])
            ->assertStatus(404)
            ->assertJsonPath('error.code', 'NOT_FOUND');

        $this->getJson("/api/v1/chat/conversations/{$conversation}/messages", ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u3'])
            ->assertStatus(404);

        $this->postJson("/api/v1/chat/conversations/{$conversation}/read", [], ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u3'])
            ->assertStatus(404);

        // B (a different platform) cannot see or touch A's thread — 404, empty list
        $this->getJson("/api/v1/chat/conversations/{$conversation}", ['Authorization' => 'Bearer '.$bearerB, 'X-External-User-Id' => 'b1'])
            ->assertStatus(404)
            ->assertJsonPath('error.code', 'NOT_FOUND');

        $this->postJson("/api/v1/chat/conversations/{$conversation}/messages", $send, ['Authorization' => 'Bearer '.$bearerB, 'X-External-User-Id' => 'b1'])
            ->assertStatus(404);

        $this->getJson('/api/v1/chat/conversations', ['Authorization' => 'Bearer '.$bearerB, 'X-External-User-Id' => 'b1'])
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_message_history_cursor_pagination_is_complete_and_gapless(): void
    {
        [$platform, , , $bearer] = $this->provision('plat-a');
        [$conversation] = $this->makeConversation($platform, $bearer, ['u1', 'u2']);

        foreach (range(0, 24) as $index) {
            $this->postJson(
                "/api/v1/chat/conversations/{$conversation}/messages",
                [
                    'client_message_id' => 'msg-client-'.str_pad((string) $index, 3, '0', STR_PAD_LEFT),
                    'content' => "message-{$index}",
                ],
                ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u1'],
            )->assertStatus(201);
        }

        $pageOne = $this->getJson("/api/v1/chat/conversations/{$conversation}/messages?limit=10", ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u2']);
        $pageOne->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.pagination.has_more', true)
            ->assertJsonPath('meta.pagination.limit', 10)
            ->assertJsonPath('data.0.content', 'message-15')
            ->assertJsonPath('data.9.content', 'message-24');

        $nextOne = $pageOne->json('meta.pagination.next_cursor');
        $this->assertIsInt($nextOne);

        $pageTwo = $this->getJson("/api/v1/chat/conversations/{$conversation}/messages?limit=10&before={$nextOne}", ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u2']);
        $pageTwo->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.pagination.has_more', true)
            ->assertJsonPath('data.0.content', 'message-5')
            ->assertJsonPath('data.9.content', 'message-14');

        $nextTwo = $pageTwo->json('meta.pagination.next_cursor');
        $this->assertIsInt($nextTwo);

        $pageThree = $this->getJson("/api/v1/chat/conversations/{$conversation}/messages?limit=10&before={$nextTwo}", ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u2']);
        $pageThree->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('meta.pagination.has_more', false)
            ->assertJsonPath('meta.pagination.next_cursor', null)
            ->assertJsonPath('data.0.content', 'message-0')
            ->assertJsonPath('data.4.content', 'message-4');

        $contents = collect($pageOne->json('data'))
            ->concat($pageTwo->json('data'))
            ->concat($pageThree->json('data'))
            ->pluck('content');

        $this->assertCount(25, $contents->unique());
        $this->assertSame(
            range(0, 24),
            $contents->map(fn (string $content) => (int) Str::after($content, 'message-'))->sort()->values()->all()
        );
    }

    public function test_history_validates_cursor_and_limit(): void
    {
        [$platform, , , $bearer] = $this->provision('plat-a');
        [$conversation] = $this->makeConversation($platform, $bearer, ['u1', 'u2']);

        $this->postJson("/api/v1/chat/conversations/{$conversation}/messages", ['client_message_id' => 'msg-client-0001', 'content' => 'x'], ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u1'])->assertStatus(201);

        $base = ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u2'];

        $this->getJson("/api/v1/chat/conversations/{$conversation}/messages?limit=0", $base)->assertStatus(422);
        $this->getJson("/api/v1/chat/conversations/{$conversation}/messages?limit=101", $base)->assertStatus(422);
        $this->getJson("/api/v1/chat/conversations/{$conversation}/messages?before=0", $base)->assertStatus(422);
    }

    public function test_send_validates_payload(): void
    {
        [$platform, , , $bearer] = $this->provision('plat-a');
        [$conversation] = $this->makeConversation($platform, $bearer, ['u1', 'u2']);

        $this->postJson("/api/v1/chat/conversations/{$conversation}/messages", [
            'client_message_id' => 'msg-client-0001',
        ], ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u1'])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_FAILED')
            ->assertJsonValidationErrors('content', 'error.errors');

        $this->postJson("/api/v1/chat/conversations/{$conversation}/messages", [
            'client_message_id' => 'abc',
            'content' => 'x',
        ], ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u1'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('client_message_id', 'error.errors');

        $this->postJson("/api/v1/chat/conversations/{$conversation}/messages", [
            'client_message_id' => 'msg-client-0002',
            'content' => 'x',
            'type' => 'video',
        ], ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u1'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('type', 'error.errors');
    }

    public function test_conversation_list_orders_by_last_message_and_read_state(): void
    {
        [$platform, , , $bearer] = $this->provision('plat-a');
        [$convOne] = $this->makeConversation($platform, $bearer, ['u1', 'u2']);
        [$convTwo] = $this->makeConversation($platform, $bearer, ['u1', 'u3']);

        // both empty: no last_message, unread 0
        $this->getJson('/api/v1/chat/conversations', ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u1'])
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $this->travel(2)->seconds();

        $this->postJson("/api/v1/chat/conversations/{$convTwo}/messages", ['client_message_id' => 'msg-2-0001', 'content' => 'older thread'], ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u1'])->assertStatus(201);

        $this->travel(3)->seconds();

        $this->postJson("/api/v1/chat/conversations/{$convOne}/messages", ['client_message_id' => 'msg-1-0001', 'content' => 'newest thread'], ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u1'])->assertStatus(201);

        $this->getJson('/api/v1/chat/conversations', ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u1'])
            ->assertOk()
            ->assertJsonPath('data.0.id', $convOne)
            ->assertJsonPath('data.0.last_message.excerpt', 'newest thread')
            ->assertJsonPath('data.1.id', $convTwo)
            ->assertJsonPath('data.1.last_message.excerpt', 'older thread')
            ->assertJsonPath('meta.pagination.total', 2)
            ->assertJsonPath('meta.pagination.per_page', 20);

        // peer participants see unread increments; newest thread has its sender's id
        $this->getJson('/api/v1/chat/conversations', ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u2'])
            ->assertOk()
            ->assertJsonPath('data.0.unread_count', 1)
            ->assertJsonPath('data.0.last_message.sender_id', 'u1');

        $this->getJson('/api/v1/chat/conversations', ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u3'])
            ->assertOk()
            ->assertJsonPath('data.0.unread_count', 1);
    }

    public function test_mark_read_is_unread_reset_and_cursor_never_regresses(): void
    {
        [$platform, , , $bearer] = $this->provision('plat-a');
        [$conversationPublicId] = $this->makeConversation($platform, $bearer, ['u1', 'u2']);
        $conversationId = $this->internalConversationId($conversationPublicId);

        foreach (['msg-0001', 'msg-0002'] as $index => $clientId) {
            $this->travel(1)->seconds();
            $this->postJson("/api/v1/chat/conversations/{$conversationPublicId}/messages", ['client_message_id' => $clientId, 'content' => 'm'.($index + 1)], ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u1'])->assertStatus(201);
        }

        $ids = Message::query()->where('conversation_id', $conversationId)->orderBy('id')->pluck('id');
        $this->assertCount(2, $ids);
        [$m1, $m2] = [$ids[0], $ids[1]];

        // read-everything: cursor jumps to the latest
        $readAll = $this->postJson("/api/v1/chat/conversations/{$conversationPublicId}/read", [], ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u2']);
        $readAll->assertOk()
            ->assertJsonPath('data.unread_count', 0)
            ->assertJsonPath('data.last_read_message_id', $m2);
        $this->assertNotNull($readAll->json('data.last_read_at'));

        // new message → unread bumps again
        $this->travel(1)->seconds();
        $this->postJson("/api/v1/chat/conversations/{$conversationPublicId}/messages", ['client_message_id' => 'msg-0003', 'content' => 'm3'], ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u1'])->assertStatus(201);

        $this->getJson('/api/v1/chat/conversations/'.$conversationPublicId, ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u2'])
            ->assertOk()
            ->assertJsonPath('data.unread_count', 1);

        // requesting an OLDER cursor must NOT regress the read position
        $this->postJson("/api/v1/chat/conversations/{$conversationPublicId}/read", ['last_read_message_id' => $m1], ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u2'])
            ->assertOk()
            ->assertJsonPath('data.last_read_message_id', $m2)
            ->assertJsonPath('data.unread_count', 0);

        // a later message arrives; read only up to m2 → unread counts the tail
        $this->travel(1)->seconds();
        $this->postJson("/api/v1/chat/conversations/{$conversationPublicId}/messages", ['client_message_id' => 'msg-0004', 'content' => 'm4'], ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u1'])->assertStatus(201);

        $this->postJson("/api/v1/chat/conversations/{$conversationPublicId}/read", ['last_read_message_id' => $m2], ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u2'])
            ->assertOk()
            ->assertJsonPath('data.last_read_message_id', $m2)
            ->assertJsonPath('data.unread_count', 0);

        // read to the current tail
        $m4 = Message::query()->where('conversation_id', $conversationId)->orderByDesc('id')->value('id');
        $this->postJson("/api/v1/chat/conversations/{$conversationPublicId}/read", [], ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u2'])
            ->assertOk()
            ->assertJsonPath('data.last_read_message_id', $m4)
            ->assertJsonPath('data.unread_count', 0);
    }

    public function test_read_only_token_cannot_mutate_chat_but_can_read(): void
    {
        [$platform, $integration, , $bearer] = $this->provision('plat-a');

        // actors must exist before a read-scoped call can resolve them
        $this->postJson('/api/v1/users/verify', ['external_user_id' => 'u1'], ['Authorization' => 'Bearer '.$bearer])->assertOk();
        $this->postJson('/api/v1/users/verify', ['external_user_id' => 'u2'], ['Authorization' => 'Bearer '.$bearer])->assertOk();

        $readOnly = $this->mintToken($platform, $integration, ['authentication', 'realtime_chat:read']);

        $this->postJson('/api/v1/chat/conversations', [
            'participant_external_ids' => ['u1', 'u2'],
        ], ['Authorization' => 'Bearer '.$readOnly])
            ->assertStatus(403)
            ->assertJsonPath('error.code', 'SERVICE_NO_ACCESS')
            ->assertJsonPath('success', false);

        $this->assertSame(1, ApiAuditLog::query()
            ->where('event', ApiAuditLog::EVENT_INSUFFICIENT_SCOPE)
            ->where('platform_id', $platform->id)
            ->where('endpoint', 'api/v1/chat/conversations')
            ->where('status_code', 403)
            ->count());

        // read scope IS allowed: the empty list resolves without disruption
        $this->getJson('/api/v1/chat/conversations', ['Authorization' => 'Bearer '.$readOnly, 'X-External-User-Id' => 'u1'])
            ->assertOk()
            ->assertJsonCount(0, 'data');

        // full-scope bearer seeds a thread; read-only token can then view it
        $created = $this->postJson('/api/v1/chat/conversations', ['participant_external_ids' => ['u1', 'u2']], ['Authorization' => 'Bearer '.$bearer])->assertOk();
        $conversation = $created->json('data.id');

        $this->getJson('/api/v1/chat/conversations/'.$conversation, ['Authorization' => 'Bearer '.$readOnly, 'X-External-User-Id' => 'u1'])
            ->assertOk()
            ->assertJsonPath('data.id', $conversation);
    }

    public function test_chat_endpoints_are_audited_under_the_shared_stack(): void
    {
        [$platform, , , $bearer] = $this->provision('plat-a');
        [$conversation] = $this->makeConversation($platform, $bearer, ['u1', 'u2']);

        $this->postJson("/api/v1/chat/conversations/{$conversation}/messages", ['client_message_id' => 'msg-client-0001', 'content' => 'x'], ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u1'])->assertStatus(201);
        $this->getJson("/api/v1/chat/conversations/{$conversation}/messages", ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u2'])->assertOk();
        $this->postJson("/api/v1/chat/conversations/{$conversation}/read", [], ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'u2'])->assertOk();

        $rows = ApiAuditLog::query()
            ->where('event', ApiAuditLog::EVENT_REQUEST)
            ->where('platform_id', $platform->id)
            ->where('endpoint', 'like', 'api/v1/chat/%')
            ->get();

        $this->assertCount(4, $rows);
        $this->assertSame(200, $rows[0]->status_code);
    }

    public function test_missing_or_unknown_actor_header_is_rejected(): void
    {
        [$platform, , , $bearer] = $this->provision('plat-a');
        [$conversation] = $this->makeConversation($platform, $bearer, ['u1', 'u2']);

        // missing X-External-User-Id on an actor-scoped endpoint
        $this->getJson("/api/v1/chat/conversations/{$conversation}/messages", ['Authorization' => 'Bearer '.$bearer])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_FAILED')
            ->assertJsonStructure(['error' => ['request_id']]);

        // a value that has NO mapping on this platform resolves to 404 (isolation)
        $this->postJson("/api/v1/chat/conversations/{$conversation}/messages", [
            'client_message_id' => 'msg-client-0001',
            'content' => 'x',
        ], ['Authorization' => 'Bearer '.$bearer, 'X-External-User-Id' => 'ghost-user'])
            ->assertStatus(404)
            ->assertJsonPath('error.code', 'NOT_FOUND');
    }

    /**
     * @return array{0: Platform, 1: PlatformIntegration, 2: string, 3: string}
     *                                                                          [platform, integration, oneTimeSecret, bearer]
     */
    private function provision(string $slug = 'chatband'): array
    {
        [$platform, $integration, $secret] = $this->app
            ->make(PlatformOnboardingService::class)
            ->onboard($slug);

        $this->assertNotNull($secret);
        /** @var string $secret */
        $response = $this->postJson('/api/v1/auth/token', [
            'client_id' => $platform->public_id,
            'client_secret' => $secret,
        ]);
        $response->assertOk();

        return [$platform, $integration, $secret, (string) $response->json('data.access_token')];
    }

    /**
     * Seed a resolved conversation through the real HTTP surface and return its
     * public ULID.
     *
     * @param  array{0: string, 1: string}  $externalIds
     * @return array{0: string}
     */
    private function makeConversation(Platform $platform, string $bearer, array $externalIds): array
    {
        foreach ($externalIds as $id) {
            $this->postJson('/api/v1/users/verify', ['external_user_id' => $id], ['Authorization' => 'Bearer '.$bearer])->assertOk();
        }

        $response = $this->postJson('/api/v1/chat/conversations', [
            'participant_external_ids' => $externalIds,
        ], ['Authorization' => 'Bearer '.$bearer]);

        $response->assertOk();

        return [(string) $response->json('data.id')];
    }

    private function internalConversationId(string $publicId): ?int
    {
        $conversationId = Conversation::query()->where('public_id', $publicId)->value('id');

        return $conversationId === null ? null : (int) $conversationId;
    }

    /**
     * @param  list<string>  $scopes
     */
    private function mintToken(Platform $platform, PlatformIntegration $integration, array $scopes): string
    {
        $key = $integration->primaryApiKey();
        $this->assertNotNull($key);

        $issued = $this->app->make(PasetoTokenService::class)->issue($integration, $key, $scopes);

        return $issued->token;
    }
}
