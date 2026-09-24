<x-layout.auth :title="$title" :subtitle="$subtitle">
    <form method="POST" action="{{ route('password.email') }}" class="space-y-5" data-submit-guard>
        @csrf

        <x-field type="email" label="Email address" name="email" required autocomplete="email" placeholder="you@platform.in" inputmode="email" autofocus />

        <x-button type="submit" variant="primary" class="w-full" icon="arrow-right">
            Send reset link
        </x-button>
    </form>

    <x-slot:footer>
        <p class="text-center text-sm text-ink/55">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 font-semibold text-brand-700 hover:text-brand-800">
                <x-icon name="arrow-left" class="h-4 w-4" /> Back to sign in
            </a>
        </p>
    </x-slot:footer>
</x-layout.auth>