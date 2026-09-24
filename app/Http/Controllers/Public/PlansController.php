<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Service;
use Illuminate\View\View;

class PlansController extends Controller
{
    /**
     * GET /plans — DB-driven plan catalog (ADR-012). Only plans linked to an
     * active service and marked active are shown. Always reflects what the
     * entitlement engine actually grants, so pricing/limits can never drift
     * from what a subscribing platform truly receives.
     */
    public function __invoke(): View
    {
        $plans = Plan::query()
            ->where('is_active', true)
            ->with('service')
            ->get()
            ->filter(fn (Plan $plan) => $plan->service?->is_active)
            ->values();

        return view('pages.plans', [
            'plans' => $plans,
            'meta' => [
                'title' => 'Plans & Pricing',
                'description' => 'Simple, transparent plans for matrimony platforms — start free, scale as your member base grows.',
            ],
        ]);
    }
}
