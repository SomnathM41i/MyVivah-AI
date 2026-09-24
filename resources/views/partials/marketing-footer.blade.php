<footer class="border-t border-ink/8 bg-white">
    <div class="container-x grid gap-10 py-14 sm:grid-cols-2 lg:grid-cols-4">
        <div class="sm:col-span-2 lg:col-span-1">
            <x-logo />
            <p class="mt-4 max-w-xs text-sm leading-relaxed text-ink/60">
                The AI &amp; API platform for matrimony websites. Add real-time chat, automation and
                AI-powered services to your existing platform — without re-platforming.
            </p>
        </div>

        <div>
            <h3 class="text-sm font-semibold text-ink">Product</h3>
            <ul class="mt-4 space-y-2.5 text-sm">
                <li><a href="{{ route('services.index') }}" class="text-ink/60 transition-colors hover:text-brand-700">Services</a></li>
                <li><a href="{{ route('plans.index') }}" class="text-ink/60 transition-colors hover:text-brand-700">Plans</a></li>
                <li><a href="{{ route('about') }}" class="text-ink/60 transition-colors hover:text-brand-700">About MyVivahAI</a></li>
                <li><a href="{{ route('signup') }}" class="text-ink/60 transition-colors hover:text-brand-700">Get Started</a></li>
            </ul>
        </div>

        <div>
            <h3 class="text-sm font-semibold text-ink">Company</h3>
            <ul class="mt-4 space-y-2.5 text-sm">
                <li><a href="{{ route('contact') }}" class="text-ink/60 transition-colors hover:text-brand-700">Contact</a></li>
                <li><a href="{{ route('contact') }}" class="text-ink/60 transition-colors hover:text-brand-700">Support</a></li>
                <li><a href="mailto:{{ config('mail.to.address') }}" class="text-ink/60 transition-colors hover:text-brand-700">{{ config('mail.to.address') }}</a></li>
            </ul>
        </div>

        <div>
            <h3 class="text-sm font-semibold text-ink">For platforms</h3>
            <ul class="mt-4 space-y-2.5 text-sm">
                <li class="flex items-center gap-2 text-ink/60"><x-icon name="chat" class="h-4 w-4" /> Real-time chat &amp; messaging</li>
                <li class="flex items-center gap-2 text-ink/60"><x-icon name="code" class="h-4 w-4" /> REST APIs for any stack</li>
                <li class="flex items-center gap-2 text-ink/60"><x-icon name="shield" class="h-4 w-4" /> Platform-isolated &amp; secure</li>
            </ul>
        </div>
    </div>

    <div class="border-t border-ink/8">
        <div class="container-x flex flex-col items-center justify-between gap-3 py-6 text-xs text-ink/50 sm:flex-row">
            <p>&copy; {{ date('Y') }} MyVivahAI. All rights reserved.</p>
            <p>Built for matrimony platforms. Privacy and security built in.</p>
        </div>
    </div>
</footer>