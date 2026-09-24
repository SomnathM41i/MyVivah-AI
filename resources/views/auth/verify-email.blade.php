<x-layout.auth :title="$title" :subtitle="$subtitle">
    <div class="space-y-5">
        <div class="rounded-xl border border-accent-200 bg-accent-50 p-4 text-sm text-accent-800">
            <div class="flex items-start gap-3">
                <x-icon name="mail" class="mt-0.5 h-5 w-5 shrink-0" />
                <p>
                    We sent a verification link to <strong>{{ $email }}</strong>. Click it to verify your
                    email and activate your account. The link expires in 60 minutes.
                </p>
            </div>
        </div>

        <p class="text-center text-sm text-ink/45">
            Didn't get the email? Check your spam folder, or resend below.
        </p>

        <form method="POST" action="{{ route('verification.send') }}" class="space-y-4" data-submit-guard>
            @csrf
            <x-field type="email" label="Email address" name="email" required value="{{ $email }}" autocomplete="email" placeholder="you@platform.in" inputmode="email" />
            <x-button type="submit" variant="primary" class="w-full" icon="refresh">
                Resend verification link
            </x-button>
        </form>

        <p class="text-center text-sm text-ink/55">
            Verified already?
            <a href="{{ route('login') }}" class="font-semibold text-brand-700 hover:text-brand-800">Sign in</a>
        </p>
    </div>
</x-layout.auth>