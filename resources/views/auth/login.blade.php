<x-layout.auth :title="$title" :subtitle="$subtitle">
    <form method="POST" action="{{ route('login.store') }}" class="space-y-5" data-submit-guard>
        @csrf

        <x-field type="email" label="Email address" name="email" required autocomplete="email" placeholder="you@platform.in" inputmode="email" autofocus />
        <x-field type="password" label="Password" name="password" required autocomplete="current-password" placeholder="••••••••" />

        <div class="flex items-center justify-between">
            <label for="remember" class="flex cursor-pointer items-center gap-2 text-sm text-ink/65">
                <input
                    id="remember"
                    type="checkbox"
                    name="remember"
                    value="1"
                    class="h-4 w-4 rounded border-ink/20 text-brand-600 focus-ring"
                >
                Remember me
            </label>
            <a href="{{ route('password.request') }}" class="text-sm font-medium text-brand-700 hover:text-brand-800">
                Forgot password?
            </a>
        </div>

        <x-button type="submit" variant="primary" class="w-full" icon="arrow-right">
            Sign in
        </x-button>
    </form>

    <x-slot:footer>
        <p class="text-center text-sm text-ink/55">
            New to MyVivahAI?
            <a href="{{ route('signup') }}" class="font-semibold text-brand-700 hover:text-brand-800">Create an account</a>
        </p>
    </x-slot:footer>
</x-layout.auth>