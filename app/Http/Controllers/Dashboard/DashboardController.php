<?php

namespace App\Http\Controllers\Dashboard;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Models\Platform;
use App\Models\Subscription;
use App\Services\ExternalPlatformUserSearch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
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
            'searchEndpoint' => $platform?->integration?->user_search_endpoint,
            'searchAuthType' => $platform?->integration?->user_search_auth_type,
            'searchHeader' => $platform?->integration?->user_search_auth_header,
            'searchSecretConfigured' => filled($platform?->integration?->user_search_auth_secret),
            'breadcrumbs' => ['Dashboard' => null],
        ]);
    }

    public function updateSearchIntegration(Request $request): RedirectResponse
    {
        $platform = $this->platformFor($request);
        abort_unless($platform !== null, 404);
        $integration = $platform->integration;
        abort_unless($integration !== null, 404);

        $data = $request->validate([
            'user_search_endpoint' => ['required', 'url', 'starts_with:https://', 'max:2048'],
            'user_search_auth_type' => ['required', 'in:bearer,header'],
            'user_search_auth_header' => ['nullable', 'required_if:user_search_auth_type,header', 'regex:/^(?!(?:host|content-length|connection|accept|x-myvivaai-platform-id|x-myvivaai-request-id|x-myvivaai-requester-id)$)[A-Za-z][A-Za-z0-9-]{0,99}$/i'],
            'user_search_auth_secret' => ['nullable', 'string', 'max:2000'],
            'allowed_search_origin' => ['required', 'url', 'starts_with:https://', 'max:255'],
        ]);

        $integration->user_search_endpoint = $data['user_search_endpoint'];
        $integration->user_search_auth_type = $data['user_search_auth_type'];
        $integration->user_search_auth_header = $data['user_search_auth_type'] === 'header' ? $data['user_search_auth_header'] : null;
        $searchHost = strtolower((string) parse_url($data['allowed_search_origin'], PHP_URL_HOST));
        abort_if($searchHost === '', 422);
        abort_unless($searchHost === strtolower((string) parse_url($data['user_search_endpoint'], PHP_URL_HOST)), 422);
        $integration->user_search_allowed_hosts = array_values(array_unique(array_merge($integration->user_search_allowed_hosts ?? [], [$searchHost])));
        if (filled($data['user_search_auth_secret'] ?? null)) {
            $integration->user_search_auth_secret = Crypt::encryptString($data['user_search_auth_secret']);
        }
        $integration->save();

        return back()->with('status', 'User-search integration saved. The credential is encrypted and will not be shown again.');
    }

    public function testSearchIntegration(Request $request, ExternalPlatformUserSearch $search): RedirectResponse
    {
        $platform = $this->platformFor($request);
        abort_unless($platform !== null && $platform->integration !== null, 404);
        $data = $request->validate([
            'requester_external_user_id' => ['required', 'string', 'between:1,255', 'regex:/^[^\/]+$/u'],
            'query' => ['required', 'string', 'min:2', 'max:100'],
        ]);

        try {
            $result = $search->search($platform, $data['requester_external_user_id'], $data['query'], 1, null);
        } catch (ApiException $exception) {
            return back()->withErrors(['search_test' => $exception->getMessage()]);
        }

        $detail = $result['results'] === [] ? 'no eligible users for this sample query.' : count($result['results']).' eligible sample result(s).';

        return back()->with('search_test_status', 'Connection succeeded; '.$detail);
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
        $user = $request->user();
        if ($user === null) {
            return null;
        }

        $ownedPlatform = $user->platforms()->latest('id')->first();
        if ($ownedPlatform !== null) {
            return $ownedPlatform;
        }

        return Platform::query()
            ->whereHas('platformAdmins', function ($query) use ($user): void {
                $query->where('user_id', $user->id)
                    ->whereNotNull('accepted_at')
                    ->whereIn('role', ['owner', 'admin', 'developer']);
            })
            ->latest('id')
            ->first();
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
