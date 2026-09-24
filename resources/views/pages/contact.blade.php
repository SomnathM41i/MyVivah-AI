<x-layout.guest
    :title="$meta['title']"
    :description="$meta['description']"
>
    <section class="relative overflow-hidden border-b border-ink/8">
        <div class="bg-grid absolute inset-0 opacity-70" aria-hidden="true"></div>
        <div class="container-x relative py-20 text-center sm:py-24">
            <p class="animate-fade-up inline-flex items-center gap-2 rounded-full border border-brand-200 bg-brand-50 px-4 py-1.5 text-xs font-semibold text-brand-700">
                <x-icon name="mail" class="h-4 w-4" />
                Contact us
            </p>
            <h1 class="animate-fade-up mx-auto mt-6 max-w-3xl text-4xl font-semibold tracking-tight text-ink sm:text-5xl" style="animation-delay:80ms">
                Let's talk about <span class="text-gradient">your platform</span>
            </h1>
            <p class="animate-fade-up mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-ink/60" style="animation-delay:160ms">
                Questions, sales, integrations or roadmap requests — we reply to every message personally.
            </p>
        </div>
    </section>

    <section class="container-x grid gap-10 py-20 sm:py-24 lg:grid-cols-5">
        {{-- Form --}}
        <div class="lg:col-span-3">
            <x-card>
                <h2 class="text-xl font-semibold text-ink">Send us a message</h2>
                <p class="mt-1.5 text-sm text-ink/55">We usually reply within one business day.</p>

                <form method="POST" action="{{ route('contact.store') }}" class="mt-8 space-y-5" data-submit-guard>
                    @csrf

                    <div class="grid gap-5 sm:grid-cols-2">
                        <x-field label="Your name" name="name" required placeholder="Asha Sharma" autocomplete="name" />
                        <x-field label="Company / platform" name="company" placeholder="Your matrimony website" autocomplete="organization" />
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <x-field type="email" label="Email address" name="email" required placeholder="you@platform.in" autocomplete="email" inputmode="email" />
                        <x-field label="Phone (optional)" name="phone" placeholder="+91 98765 43210" autocomplete="tel" inputmode="tel" />
                    </div>

                    <div>
                        <label for="message" class="mb-1.5 block text-sm font-medium text-ink-soft">
                            How can we help? <span class="text-brand-600">*</span>
                        </label>
                        <textarea
                            id="message"
                            name="message"
                            rows="5"
                            required
                            minlength="10"
                            maxlength="5000"
                            placeholder="Tell us about your platform, your members, and what you'd like to build."
                            @error('message')
                                aria-invalid="true"
                                aria-describedby="message-error"
                            @enderror
                            class="block w-full rounded-xl border border-ink/10 bg-white px-3.5 py-2.5 text-sm text-ink shadow-sm placeholder:text-ink/35 focus-ring @error('message') border-brand-500 @enderror"
                        ></textarea>
                        @error('message')
                            <p id="message-error" class="mt-1 text-xs font-medium text-brand-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Honeypot: hidden from humans, a magnet for bots. --}}
                    <div class="hidden" aria-hidden="true">
                        <label for="website">Leave this field empty</label>
                        <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-xs text-ink/45">We'll only use your details to reply. No newsletters, no spam.</p>
                        <x-button type="submit" variant="primary" icon="arrow-right" class="w-full sm:w-auto">
                            Send message
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>

        {{-- Contact info --}}
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-2xl border border-ink/8 bg-white p-6">
                <h3 class="text-sm font-semibold uppercase tracking-wider text-ink/55">Direct email</h3>
                <a href="mailto:{{ config('mail.from.address') }}" class="mt-3 inline-flex items-center gap-2 font-semibold text-brand-700 hover:text-brand-800">
                    <x-icon name="mail" class="h-5 w-5" /> {{ config('mail.from.address') }}
                </a>
            </div>

            <div class="rounded-2xl border border-ink/8 bg-white p-6">
                <h3 class="text-sm font-semibold uppercase tracking-wider text-ink/55">Need something specific?</h3>
                <ul class="mt-4 space-y-3 text-sm text-ink/65">
                    <li class="flex items-start gap-3">
                        <x-icon name="credit-card" class="mt-0.5 h-5 w-5 shrink-0 text-brand-600" />
                        <span><strong class="font-semibold text-ink">Sales &amp; invoicing</strong> — volumes, enterprise plans, vendorship.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-icon name="plug" class="mt-0.5 h-5 w-5 shrink-0 text-brand-600" />
                        <span><strong class="font-semibold text-ink">Technical questions</strong> — API docs, integration help, sandboxes.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-icon name="heart" class="mt-0.5 h-5 w-5 shrink-0 text-brand-600" />
                        <span><strong class="font-semibold text-ink">Product feedback</strong> — tell us what to build next.</span>
                    </li>
                </ul>
            </div>

            <div class="rounded-2xl border border-brand-200 bg-brand-50 p-6">
                <h3 class="text-sm font-semibold text-ink">Prefer to get started immediately?</h3>
                <p class="mt-2 text-sm leading-relaxed text-ink/60">
                    Create your free account and explore the platform workspace in minutes.
                </p>
                <x-button href="{{ route('signup') }}" variant="primary" size="sm" icon="arrow-right" class="mt-4">
                    Create free account
                </x-button>
            </div>
        </div>
    </section>
</x-layout.guest>