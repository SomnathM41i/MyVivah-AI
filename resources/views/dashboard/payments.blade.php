<x-layout.dashboard title="Payments" :breadcrumbs="$breadcrumbs">
    @include('partials.dashboard.platform-summary', ['platform' => $platform])

    @if ($payments->isEmpty())
        <x-card class="max-w-2xl">
            <div class="text-center">
                <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-brand-50 text-brand-700">
                    <x-icon name="credit-card" class="h-7 w-7" />
                </span>
                <h3 class="mt-5 text-lg font-semibold text-ink">No payments yet</h3>
                <p class="mx-auto mt-2 max-w-md text-sm leading-relaxed text-ink/60">
                    When you subscribe to a plan, invoices appear here automatically. In the current
                    phase subscriptions are activated with our help and invoiced manually.
                </p>
                <div class="mt-6 flex flex-wrap justify-center gap-3">
                    <x-button href="{{ route('dashboard.subscription') }}" variant="primary" size="sm">View subscriptions</x-button>
                    <x-button href="{{ route('contact') }}" variant="secondary" size="sm">Request an invoice</x-button>
                </div>
            </div>
        </x-card>
    @else
        <x-card>
            <div class="flex items-center justify-between">
                <h3 class="font-semibold text-ink">Payment history</h3>
                <x-badge tone="neutral">{{ $payments->count() }} records</x-badge>
            </div>

            <div class="mt-5 overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-ink/8 text-xs uppercase tracking-wide text-ink/45">
                            <th class="py-2.5 pr-4 font-medium">Transaction</th>
                            <th class="py-2.5 pr-4 font-medium">Date</th>
                            <th class="py-2.5 pr-4 font-medium">Gateway</th>
                            <th class="py-2.5 pr-4 font-medium">Amount</th>
                            <th class="py-2.5 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $payment)
                            <tr class="border-b border-ink/5">
                                <td class="py-3 pr-4 font-mono text-xs text-ink">{{ $payment->gateway_transaction_id ?? '—' }}</td>
                                <td class="py-3 pr-4 text-ink/60">{{ $payment->paid_at?->format('d M Y, H:i') ?? $payment->created_at->format('d M Y') }}</td>
                                <td class="py-3 pr-4 text-ink/60">{{ $payment->gateway === 'manual' ? 'Manual' : ucfirst($payment->gateway) }}</td>
                                <td class="py-3 pr-4 font-medium text-ink">
                                    {{ in_array($payment->currency, ['INR', 'inr'], true) ? '₹' : ($payment->currency.' ') }}{{ number_format((float) $payment->amount, 2) }}
                                </td>
                                <td class="py-3">
                                    <x-badge :tone="match ($payment->status) { 'completed' => 'success', 'failed' => 'danger', 'refunded' => 'warning', default => 'neutral' }">
                                        {{ ucfirst($payment->status) }}
                                    </x-badge>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
    @endif
</x-layout.dashboard>