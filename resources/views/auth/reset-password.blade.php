<x-layout.auth :title="$title" :subtitle="$subtitle">
    <form method="POST" action="{{ route('password.store') }}" class="space-y-5" data-submit-guard>
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

        <x-field type="email" label="Email address" name="email" required autocomplete="email" value="{{ $email }}" disabled="" class="opacity-70" />
        <x-field type="password" label="New password" name="password" required autocomplete="new-password" placeholder="8+ characters" autofocus />
        <x-field type="password" label="Confirm new password" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat your password" />

        <x-button type="submit" variant="primary" class="w-full" icon="arrow-right">
            Reset password
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