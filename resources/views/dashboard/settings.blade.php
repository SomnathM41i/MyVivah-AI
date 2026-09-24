<x-layout.dashboard title="Settings" :breadcrumbs="$breadcrumbs">
    @include('partials.dashboard.platform-summary', ['platform' => $platform])

    <div class="grid gap-6 lg:grid-cols-2">
        <x-card>
            <h3 class="font-semibold text-ink">Account profile</h3>
            <p class="mt-1 text-sm text-ink/55">Your login and profile details.</p>

            <dl class="mt-6 space-y-4 text-sm">
                <div class="flex items-center justify-between gap-4 border-b border-ink/5 pb-3">
                    <dt class="text-ink/55">Name</dt>
                    <dd class="font-medium text-ink">{{ $user->name }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-b border-ink/5 pb-3">
                    <dt class="text-ink/55">Email</dt>
                    <dd class="font-medium text-ink">{{ $user->email }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-b border-ink/5 pb-3">
                    <dt class="text-ink/55">Email verified</dt>
                    <dd>
                        @if ($user->hasVerifiedEmail())
                            <x-badge tone="success" dot>Verified</x-badge>
                        @else
                            <x-badge tone="warning" dot>Pending</x-badge>
                        @endif
                    </dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-ink/55">Last sign-in</dt>
                    <dd class="font-medium text-ink">{{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</dd>
                </div>
            </dl>
        </x-card>

        <x-card>
            <h3 class="font-semibold text-ink">Platform profile</h3>
            <p class="mt-1 text-sm text-ink/55">Details of this workspace's platform.</p>

            @if ($platform)
                <dl class="mt-6 space-y-4 text-sm">
                    <div class="flex items-center justify-between gap-4 border-b border-ink/5 pb-3">
                        <dt class="text-ink/55">Platform name</dt>
                        <dd class="font-medium text-ink">{{ $platform->name }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4 border-b border-ink/5 pb-3">
                        <dt class="text-ink/55">Slug</dt>
                        <dd class="font-mono text-xs text-ink/70">{{ $platform->slug }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4 border-b border-ink/5 pb-3">
                        <dt class="text-ink/55">Website</dt>
                        <dd class="truncate font-medium text-ink">{{ $platform->website_url ?? '—' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-ink/55">Public ID</dt>
                        <dd class="font-mono text-xs text-ink/70">{{ $platform->public_id }}</dd>
                    </div>
                </dl>
            @else
                <p class="mt-4 text-sm leading-relaxed text-ink/60">
                    No platform linked to this account yet.
                </p>
            @endif

            <p class="mt-6 rounded-xl bg-ink/5 p-4 text-sm text-ink/55">
                <x-icon name="lock" class="mr-1.5 inline h-4 w-4" />
                Self-service profile editing arrives in a later phase. Contact support to update your details.
            </p>
        </x-card>
    </div>
</x-layout.dashboard>