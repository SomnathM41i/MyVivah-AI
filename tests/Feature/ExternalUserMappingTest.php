<?php

namespace Tests\Feature;

use App\Models\ExternalUserMap;
use App\Models\Platform;
use App\Models\PlatformApiKey;
use App\Models\PlatformIntegration;
use App\Models\PlatformServiceAccess;
use App\Models\Service;
use App\Models\User;
use App\Services\ExternalUserService;
use App\Services\PasetoTokenService;
use DateTimeImmutable;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ParagonIE\Paseto\Keys\Version4\SymmetricKey;
use Tests\TestCase;

/**
 * External user identity mapping + identity endpoints
 * (phase-3a-api-integration-plan.md §8/§9; platform-isolation.md).
 *
 * Core guarantees: UNIQUE(platform_id, external_user_id), strict per-platform
 * isolation (platform always derived from the verified token), minimal data
 * (no profile mirroring), and correct scope enforcement.
 */
class ExternalUserMappingTest extends TestCase
{
    use RefreshDatabase;

    private Platform $platform;

    private PlatformIntegration $integration;

    private string $secret;

    private string $bearer;

    protected function setUp(): void
    {
        parent::setUp();

        [$this->platform, $this->integration, $this->secret] = $this->provisionedIntegration();
        $this->bearer = $this->issueBearer();
    }

    public function test_verify_creates_mapping_and_returns_reference(): void
    {
        $response = $this->postJson('/api/v1/users/verify', [
            'external_user_id' => '4582',
        ], ['Authorization' => 'Bearer '.$this->bearer]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.external_user_id', '4582')
            ->assertJsonPath('data.platform.public_id', $this->platform->public_id)
            ->assertJsonPath('meta.created', true)
            ->assertJsonMissingPath('data.name')
            ->assertJsonMissingPath('data.email');

        $this->assertSame(1, ExternalUserMap::query()->count());
    }

    public function test_verify_is_idempotent_and_updates_local_link(): void
    {
        $user = User::factory()->create();

        $this->postJson('/api/v1/users/verify', [
            'external_user_id' => '4582',
        ], ['Authorization' => 'Bearer '.$this->bearer])->assertOk();

        $response = $this->postJson('/api/v1/users/verify', [
            'external_user_id' => '4582',
            'local_public_id' => $user->public_id,
        ], ['Authorization' => 'Bearer '.$this->bearer]);

        $response->assertOk()
            ->assertJsonPath('meta.created', false)
            ->assertJsonPath('data.local_public_id', $user->public_id);

        $this->assertSame(1, ExternalUserMap::query()->count());
    }

    public function test_verify_requires_external_user_id(): void
    {
        $this->postJson('/api/v1/users/verify', [], ['Authorization' => 'Bearer '.$this->bearer])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_FAILED')
            ->assertJsonStructure(['error' => ['errors' => ['external_user_id']]]);
    }

    public function test_show_resolves_a_mapped_user(): void
    {
        $map = ExternalUserMap::factory()->forPlatform($this->platform)->create([
            'external_user_id' => 'discodj-7',
        ]);

        $this->getJson('/api/v1/users/discodj-7', ['Authorization' => 'Bearer '.$this->bearer])
            ->assertOk()
            ->assertJsonPath('data.external_user_id', 'discodj-7')
            ->assertJsonPath('data.platform.public_id', $this->platform->public_id)
            ->assertJsonPath('data.local_public_id', $map->local_public_id);
    }

    public function test_show_returns_404_for_unmapped_user(): void
    {
        $this->getJson('/api/v1/users/999999', ['Authorization' => 'Bearer '.$this->bearer])
            ->assertStatus(404)
            ->assertJsonPath('error.code', 'NOT_FOUND')
            ->assertJsonPath('success', false);
    }

    public function test_cross_platform_resolution_is_impossible(): void
    {
        // Platform A maps the external user.
        $this->postJson('/api/v1/users/verify', [
            'external_user_id' => '4582',
        ], ['Authorization' => 'Bearer '.$this->bearer])->assertOk();

        // Platform B cannot resolve (or overwrite) A's mapping.
        [$platformB, $integrationB, $secretB] = $this->provisionedIntegration('realtime_chat');
        $bearerB = $this->issueBearer($secretB, ['client_id' => $platformB->public_id]);

        $this->getJson('/api/v1/users/4582', ['Authorization' => 'Bearer '.$bearerB])
            ->assertStatus(404)
            ->assertJsonPath('error.code', 'NOT_FOUND');

        // B verifying the same external id creates its OWN isolated row.
        $this->postJson('/api/v1/users/verify', [
            'external_user_id' => '4582',
        ], ['Authorization' => 'Bearer '.$bearerB])->assertOk()
            ->assertJsonPath('data.platform.public_id', $platformB->public_id);

        $rows = ExternalUserMap::query()->orderBy('platform_id')->get();
        $this->assertSame(2, $rows->count());
        $this->assertSame(
            [$this->platform->id, $platformB->id],
            $rows->map(fn (ExternalUserMap $row) => $row->platform_id)->all()
        );
    }

    public function test_identity_endpoints_require_service_scopes(): void
    {
        // Token carrying only the bootstrap `authentication` scope.
        $limited = $this->issueBearer($this->secret, ['client_id' => $this->platform->public_id, 'scope' => ['authentication']]);

        $this->getJson('/api/v1/users/4582', ['Authorization' => 'Bearer '.$limited])
            ->assertStatus(403)
            ->assertJsonPath('error.code', 'SERVICE_NO_ACCESS');

        $this->postJson('/api/v1/users/verify', ['external_user_id' => '4582'], ['Authorization' => 'Bearer '.$limited])
            ->assertStatus(403)
            ->assertJsonPath('error.code', 'SERVICE_NO_ACCESS');
    }

    public function test_identity_endpoints_reject_malformed_and_expired_tokens(): void
    {
        $this->getJson('/api/v1/users/4582', ['Authorization' => 'Bearer not-a-paseto-token'])
            ->assertStatus(401)
            ->assertJsonPath('error.code', 'INVALID_TOKEN');

        $expired = $this->tokens()->issue(
            $this->integration,
            $this->integration->primaryApiKey(),
            ['authentication', 'realtime_chat:read'],
            new DateTimeImmutable('-2 hours'),
        );

        $this->getJson('/api/v1/users/4582', ['Authorization' => 'Bearer '.$expired->token])
            ->assertStatus(401)
            ->assertJsonPath('error.code', 'TOKEN_EXPIRED');
    }

    public function test_service_upsert_is_idempotent_across_calls(): void
    {
        $service = $this->app->make(ExternalUserService::class);

        $first = $service->resolveOrCreate($this->platform, 'u-100', null, ['source' => 'sync']);
        $second = $service->resolveOrCreate($this->platform, 'u-100', null, ['source' => 'sync']);

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, ExternalUserMap::query()->count());
        $this->assertSame(['source' => 'sync'], $second->metadata);
    }

    public function test_database_enforces_unique_platform_external_user_identity(): void
    {
        ExternalUserMap::factory()->forPlatform($this->platform)->create([
            'external_user_id' => 'dup-1',
        ]);

        try {
            ExternalUserMap::query()->create([
                'platform_id' => $this->platform->id,
                'external_user_id' => 'dup-1',
                'synced_at' => now(),
                'last_seen_at' => now(),
            ]);
            $this->fail('Expected a unique-constraint violation.');
        } catch (QueryException $ex) {
            $this->assertTrue(true);
        }

        // Same external id IS permitted for a different platform.
        $otherPlatform = Platform::factory()->active()->create();
        ExternalUserMap::query()->create([
            'platform_id' => $otherPlatform->id,
            'external_user_id' => 'dup-1',
            'synced_at' => now(),
            'last_seen_at' => now(),
        ]);

        $this->assertSame(2, ExternalUserMap::query()->count());
    }

    /**
     * @return array{0: Platform, 1: PlatformIntegration, 2: string}
     */
    private function provisionedIntegration(): array
    {
        $platform = Platform::factory()->active()->create();

        $integration = PlatformIntegration::factory()
            ->forPlatform($platform)
            ->active()
            ->create();

        $key = new SymmetricKey(random_bytes(32));

        PlatformApiKey::factory()
            ->forIntegration($integration)
            ->withRawSecret($key->encode())
            ->create();

        // `realtime_chat` is a GLOBAL service catalog row shared by every platform;
        // each platform gets its own platform_service_access entitlement.
        $service = Service::query()->firstOrCreate(
            ['key' => 'realtime_chat'],
            ['name' => 'Real-Time Chat', 'description' => 'Real-Time Chat service', 'is_active' => true, 'sort_order' => 0],
        );

        PlatformServiceAccess::factory()->granted()->create([
            'platform_id' => $platform->id,
            'service_id' => $service->id,
        ]);

        return [$platform, $integration, $key->encode()];
    }

    private function issueBearer(?string $secret = null, array $payload = []): string
    {
        $response = $this->postJson('/api/v1/auth/token', array_merge([
            'client_id' => $this->platform->public_id,
            'client_secret' => $secret ?? $this->secret,
        ], $payload));

        $response->assertOk();

        return (string) $response->json('data.access_token');
    }

    private function tokens(): PasetoTokenService
    {
        return $this->app->make(PasetoTokenService::class);
    }
}
