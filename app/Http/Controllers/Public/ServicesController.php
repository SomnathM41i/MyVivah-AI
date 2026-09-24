<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\View\View;

class ServicesController extends Controller
{
    /**
     * Available services come from the `services` table (single source of truth —
     * `is_active` drives both this page AND API entitlements, so we never fake a
     * live service). The roadmap/Coming-soon group below is page-level copy only.
     *
     * @var list<array{key: string, name: string, description: string, icon: string}>
     */
    private const ROADMAP = [
        [
            'key' => 'ai_agents',
            'name' => 'AI Agents',
            'description' => 'Conversational AI agents for matchmaking assistance, member queries and onboarding flows.',
            'icon' => 'bot',
        ],
        [
            'key' => 'automation',
            'name' => 'Automation',
            'description' => 'Automated profile shortlisting, nudges and post-match follow-ups.',
            'icon' => 'zap',
        ],
        [
            'key' => 'security_monitoring',
            'name' => 'Security & Monitoring',
            'description' => 'Anomaly detection, scam-account flags and platform health dashboards.',
            'icon' => 'shield',
        ],
        [
            'key' => 'api_integration',
            'name' => 'Easy API Integration',
            'description' => 'Drop-in SDKs and webhooks for PHP, Node.js, Python and more.',
            'icon' => 'code',
        ],
        [
            'key' => 'future_ai',
            'name' => 'Future AI Services',
            'description' => 'Horoscope matching, compatibility insights and smarter search ranking — powered by AI.',
            'icon' => 'rocket',
        ],
    ];

    /**
     * GET /services — catalog page (phase-5a §3.3).
     */
    public function __invoke(): View
    {
        $available = Service::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('pages.services', [
            'available' => $available,
            'roadmap' => self::ROADMAP,
            'meta' => [
                'title' => 'Services',
                'description' => 'Explore MyVivahAI services: real-time chat and messaging today, with AI agents, automation and security services on the roadmap.',
            ],
        ]);
    }
}
