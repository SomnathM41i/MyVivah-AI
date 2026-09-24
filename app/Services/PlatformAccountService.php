<?php

namespace App\Services;

use App\Models\Platform;
use App\Models\PlatformAdmin;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Phase 5A — self-service platform account registration.
 *
 * Creates the full account root atomically: `users` + `platforms` + the
 * platform-owner `platform_admins` row (registration-flow.md §2/§4). Credentials
 * are validated + hashed upstream (FormRequest); this service is the single
 * transactional entry-point so every registration follows the same rules.
 *
 * Account/password emails (verification) are sent by the controller after this
 * service commits — never inside the transaction, so a failed send never rolls
 * back a valid account.
 */
class PlatformAccountService
{
    /** user.status for newly registered, verified-by-email accounts. */
    private const USER_STATUS_ACTIVE = 'active';

    /**
     * Register a new platform owner + their first platform.
     *
     * @param  array{name: string, email: string, password: string, platform_name: string, website_url?: ?string}  $data
     * @return array{0: User, 1: Platform}
     */
    public function register(array $data): array
    {
        return DB::transaction(function () use ($data): array {
            $user = User::query()->create([
                'name' => trim($data['name']),
                'email' => strtolower(trim($data['email'])),
                'password' => $data['password'], // hashed via the model's `hashed` cast
                'status' => self::USER_STATUS_ACTIVE,
                'timezone' => 'UTC',
                'locale' => 'en',
            ]);

            $slug = $this->uniqueSlug($data['platform_name']);

            $platform = Platform::query()->create([
                'name' => trim($data['platform_name']),
                'slug' => $slug,
                'website_url' => isset($data['website_url'])
                    ? rtrim(trim($data['website_url']), '/')
                    : null,
                'status' => Platform::STATUS_ACTIVE,
                'created_by' => $user->id,
            ]);

            PlatformAdmin::query()->create([
                'platform_id' => $platform->id,
                'user_id' => $user->id,
                'role' => 'owner',
                'invited_at' => now(),
                'accepted_at' => now(),
            ]);

            return [$user, $platform];
        });
    }

    /**
     * Human-friendly, guaranteed-unique platform slug (platforms.slug is UNIQUE).
     */
    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'platform';

        $slug = $base;
        $counter = 2;

        while (Platform::query()->withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
