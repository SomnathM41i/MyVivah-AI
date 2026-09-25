<?php

namespace App\Http\Controllers\Dashboard;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Mail\ContactMessageMail;
use App\Models\ContactMessage;
use App\Models\Plan;
use App\Models\Platform;
use App\Models\Service;
use App\Models\Subscription;
use App\Services\ApiKeyService;
use App\Services\DashboardServiceEnrollment;
use App\Services\ExternalPlatformUserSearch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
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
        $services = Service::query()
            ->where('is_active', true)
            ->with(['plans' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();
        $subscriptionsByService = $platform?->subscriptions()
            ->whereIn('status', ['active', 'pending'])
            ->with('plan')
            ->latest('id')
            ->get()
            ->groupBy('service_id') ?? collect();

        return view('dashboard.services', [
            'platform' => $platform,
            'services' => $services,
            'serviceAccess' => $platform?->serviceAccess()->get() ?? collect(),
            'activeSubscriptionByService' => $subscriptionsByService->map(fn ($items) => $items->firstWhere('status', 'active')),
            'pendingSubscriptionByService' => $subscriptionsByService->map(fn ($items) => $items->firstWhere('status', 'pending')),
            'breadcrumbs' => ['Dashboard' => null],
        ]);
    }

    public function selectPlan(Request $request, string $planPublicId, DashboardServiceEnrollment $enrollment): RedirectResponse
    {
        $platform = $this->platformFor($request);
        abort_unless($platform !== null, 404);

        $plan = Plan::query()
            ->where('public_id', $planPublicId)
            ->where('is_active', true)
            ->whereHas('service', fn ($query) => $query->where('is_active', true))
            ->firstOrFail();

        $result = $enrollment->enroll($platform, $plan);

        if ($result['status'] === 'pending') {
            if ($result['request_created'] ?? false) {
                $contact = ContactMessage::query()->create([
                    'name' => $request->user()->name,
                    'company' => $platform->name,
                    'email' => strtolower($request->user()->email),
                    'message' => sprintf(
                        'Paid plan review requested: %s (%s %s / %s) for service %s. Platform public ID: %s. A pending subscription is recorded in the dashboard.',
                        $plan->name,
                        strtoupper($plan->currency),
                        number_format((float) $plan->price, 2),
                        $plan->billing_period,
                        $plan->service?->name ?? 'service',
                        $platform->public_id,
                    ),
                    'ip_hash' => hash('sha256', $request->ip() ?? ''),
                    'source_url' => route('dashboard.subscription'),
                ]);

                if (config('mail.to.address') !== null) {
                    try {
                        Mail::to(config('mail.to.address'))->send(new ContactMessageMail($contact));
                    } catch (\Throwable $exception) {
                        report($exception);
                    }
                }
            }

            return redirect()->route('dashboard.subscription')->with('status', 'Your paid plan request has been recorded. It will remain pending until our team reviews it; no paid access has been enabled.');
        }

        $message = (float) $plan->price <= 0
            ? 'The Free plan is active. Configure your API and widget integration below.'
            : 'This plan is already active for your platform.';
        $redirect = redirect()->route('dashboard.integrations')->with('status', $message);

        if ($result['api_secret'] !== null) {
            $redirect->with('api_secret_once', $result['api_secret']);
        }

        return $redirect;
    }

    /**
     * GET /dashboard/integrations — integration health. Key material (API keys)
     * is never exposed; the screen shows what a platform can do next.
     */
    public function integrations(Request $request): View
    {
        $platform = $this->platformFor($request);
        $integration = $platform?->integration;

        return view('dashboard.integrations', [
            'platform' => $platform,
            'integration' => $integration,
            'primaryApiKey' => $integration?->primaryApiKey(),
            'canUseApi' => $platform?->serviceAccess()
                ->where('has_access', true)
                ->where(fn ($query) => $query->whereNull('effective_until')->orWhere('effective_until', '>', now()))
                ->exists() ?? false,
            'widgetOrigins' => $integration?->allowed_origins ?? [],
            'searchEndpoint' => $integration?->user_search_endpoint,
            'searchAuthType' => $integration?->user_search_auth_type,
            'searchHeader' => $integration?->user_search_auth_header,
            'searchSecretConfigured' => filled($integration?->user_search_auth_secret),
            'breadcrumbs' => ['Dashboard' => null],
        ]);
    }

    public function rotateApiKey(Request $request, ApiKeyService $keys): RedirectResponse
    {
        $platform = $this->platformFor($request);
        abort_unless($platform !== null && $platform->integration !== null, 404);
        abort_unless($platform->serviceAccess()
            ->where('has_access', true)
            ->where(fn ($query) => $query->whereNull('effective_until')->orWhere('effective_until', '>', now()))
            ->exists(), 403);

        [, $secret] = $keys->rotate($platform->integration);

        return back()
            ->with('status', 'A new API secret was created. The previous key remains valid during the configured rotation grace period.')
            ->with('api_secret_once', $secret);
    }

    public function updateWidgetOrigins(Request $request): RedirectResponse
    {
        $platform = $this->platformFor($request);
        abort_unless($platform !== null && $platform->integration !== null, 404);

        $validated = $request->validate([
            'widget_origins' => ['required', 'string', 'max:4000'],
        ]);
        $origins = array_values(array_unique(array_filter(array_map(
            static fn (string $origin): string => rtrim(trim($origin), '/'),
            preg_split('/\\R/u', $validated['widget_origins']) ?: [],
        ))));

        validator(['origins' => $origins], [
            'origins' => ['required', 'array', 'min:1', 'max:20'],
            'origins.*' => ['required', 'url', 'starts_with:https://', 'max:255'],
        ])->validate();

        foreach ($origins as $origin) {
            $parts = parse_url($origin);
            if ($parts === false || empty($parts['host']) || isset($parts['user']) || isset($parts['pass']) || isset($parts['query']) || isset($parts['fragment']) || (($parts['path'] ?? '') !== '')) {
                throw \Illuminate\Validation\ValidationException::withMessages(['widget_origins' => 'Enter only HTTPS website origins, such as https://example.com, without a path.']);
            }
        }

        $integration = $platform->integration;
        $integration->base_domain = $origins[0];
        $integration->allowed_origins = $origins;
        $integration->save();

        return back()->with('status', 'Widget website origins saved. The widget will return browser responses only to these origins.');
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
            'subscriptions' => $platform?->subscriptions()->with(['plan', 'service'])->latest('id')->get() ?? collect(),
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
