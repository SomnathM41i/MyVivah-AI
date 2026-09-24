<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Platform;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * GET /dashboard — platform overview (affordable, honest shell: no fake
     * numbers or invented entitlements; everything shown comes from the DB).
     */
    public function index(Request $request): View
    {
        $platform = $this->platformFor($request);

        return view('dashboard.index', [
            'platform' => $platform,
            'serviceAccess' => $platform->serviceAccess ?? collect(),
            'activeSubscription' => $this->activeSubscription($platform),
            'recentPayments' => $platform?->payments()->latest()->limit(5)->get() ?? collect(),
            'breadcrumbs' => [],
        ]);
    }

    /**
     * GET /dashboard/services — service catalog + this platform's entitlements.
     */
    public function services(Request $request): View
    {
        $platform = $this->platformFor($request);

        return view('dashboard.services', [
            'platform' => $platform,
            'serviceAccess' => $platform->serviceAccess ?? collect(),
            'breadcrumbs' => ['Dashboard' => null],
        ]);
    }

    /**
     * GET /dashboard/integrations — integration health. Key material (API keys)
     * is never exposed; the screen shows what a platform can do next.
     */
    public function integrations(Request $request): View
    {
        $platform = $this->platformFor($request);

        return view('dashboard.integrations', [
            'platform' => $platform,
            'integration' => $platform?->integration,
            'breadcrumbs' => ['Dashboard' => null],
        ]);
    }

    /**
     * GET /dashboard/subscription — current plan / subscription state.
     */
    public function subscription(Request $request): View
    {
        $platform = $this->platformFor($request);

        return view('dashboard.subscription', [
            'platform' => $platform,
            'activeSubscription' => $this->activeSubscription($platform),
            'breadcrumbs' => ['Dashboard' => null],
        ]);
    }

    /**
     * GET /dashboard/payments — payment history (ADR-011: manual workflow; no
     * gateway exists yet, so this is honest empty-state + expectation copy).
     */
    public function payments(Request $request): View
    {
        $platform = $this->platformFor($request);

        return view('dashboard.payments', [
            'platform' => $platform,
            'payments' => $platform?->payments()->latest()->get() ?? collect(),
            'breadcrumbs' => ['Dashboard' => null],
        ]);
    }

    /**
     * GET /dashboard/settings — account & platform profile (read-only shell;
     * self-service editing is a later phase).
     */
    public function settings(Request $request): View
    {
        $platform = $this->platformFor($request);

        return view('dashboard.settings', [
            'user' => $request->user(),
            'platform' => $platform,
            'breadcrumbs' => ['Dashboard' => null],
        ]);
    }

    /**
     * The authenticated user's latest registered platform (account root).
     */
    private function platformFor(Request $request): ?Platform
    {
        return $request->user()?->platforms()->latest('id')->first();
    }

    /**
     * Honest "current" subscription lookup: newest non-expired, non-cancelled,
     * active subscription for the platform (statuses are documented in
     * subscription-flow.md; nothing is invented on the page).
     */
    private function activeSubscription(?Platform $platform): ?Subscription
    {
        if ($platform === null) {
            return null;
        }

        return Subscription::query()
            ->where('platform_id', $platform->id)
            ->where('status', 'active')
            ->where(function ($q): void {
                $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
            })
            ->latest('id')
            ->first();
    }
}
