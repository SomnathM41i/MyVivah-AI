<x-layout.auth :title="$title" :subtitle="$subtitle">
    <form method="POST" action="{{ route('signup.store') }}" class="space-y-5" data-submit-guard>
        @csrf

        <x-field label="Your name" name="name" required placeholder="Asha Sharma" autocomplete="name" autofocus />

        <div class="grid gap-5 sm:grid-cols-2">
            <x-field label="Platform name" name="platform_name" required placeholder="namma-matrimony" hint="Shown on your workspace. A unique slug is created automatically." />
            <x-field label="Website (optional)" name="website_url" placeholder="https://..." autocomplete="url" inputmode="url" />
        </div>

        <x-field type="email" label="Work email" name="email" required placeholder="you@platform.in" autocomplete="email" inputmode="email" />
        <x-field type="password" label="Password" name="password" required autocomplete="new-password" placeholder="8+ characters" />
        <x-field type="password" label="Confirm password" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat your password" />

        <label class="flex cursor-pointer items-start gap-2.5 text-sm text-ink/65">
            <input
                type="checkbox"
                name="terms"
                value="1"
                required
                class="mt-0.5 h-4 w-4 rounded border-ink/20 text-brand-600 focus-ring"
            >
            <span>
                I agree to the Terms of Service and Privacy Policy. Your platform data stays yours —
                we never share it.
            </span>
        </label>

        <x-button type="submit" variant="primary" class="w-full" icon="arrow-right">
            Create account
        </x-button>
    </form>

    <x-slot:footer>
        <p class="text-center text-sm text-ink/55">
            Already have an account?
            <a href="{{ route('login') }}" class="font-semibold text-brand-700 hover:text-brand-800">Sign in</a>
        </p>
    </x-slot:footer>
</x-layout.auth>