<?php

namespace App\Http\Controllers\Demo;

use App\Http\Controllers\Controller;
use App\Models\Platform;
use App\Models\PlatformIntegration;
use App\Services\ExternalUserService;
use App\Services\WidgetRealtimeConfig;
use App\Services\WidgetSessionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * GET /demo/chat — LOCAL-ONLY widget embedding demo (Phase 4).
 *
 * Mirrors exactly what an external platform integration does: it mints a widget
 * session server-side for its logged-in user and hands the browser an inline
 * session. Two demo users are shown side-by-side so you can see both ends of a
 * thread, search, presence, and realtime together.
 *
 * Guard: only when local + APP_DEBUG, otherwise 404 (never exposed publicly).
 */
class ChatDemoController extends Controller
{
    public function __construct(
        private readonly WidgetSessionService $sessions,
        private readonly ExternalUserService $users,
    ) {}

    public function __invoke(Request $request): View
    {
        if ((string) config('app.env') !== 'local' || ! config('app.debug')) {
            abort(404);
        }

        $platform = Platform::query()
            ->with('integration.apiKeys')
            ->where('slug', 'demo-matrimony-site')
            ->first();

        if ($platform === null || ! $platform->integration instanceof PlatformIntegration) {
            return view('demo.chat-error', [
                'message' => 'Demo platform not found. Run `php artisan db:seed` first.',
            ]);
        }

        $apiKey = $platform->integration->primaryApiKey();
        if ($apiKey === null) {
            return view('demo.chat-error', [
                'message' => 'Demo integration has no active primary key. Re-run `php artisan db:seed`.',
            ]);
        }

        $realtime = WidgetRealtimeConfig::for();

        $sessions = [];
        foreach ([['me', 'alice@demo.example.test'], ['them', 'bob@demo.example.test']] as [$key, $externalId]) {
            $map = $this->users->resolveOrCreate($platform, $externalId);
            $sessions[$key] = $this->sessions->present(
                $this->sessions->issue($platform->integration, $apiKey, $externalId),
                $map,
                $realtime,
            );
        }

        return view('demo.chat', [
            'sessionMe' => $sessions['me'],
            'sessionThem' => $sessions['them'],
            'realtimeEnabled' => $realtime['enabled'],
            'realtimeConnection' => $realtime['enabled'] ? $realtime['connection'] : null,
            'widgetScript' => '/js/myvivah-widget.js',
        ]);
    }
}
