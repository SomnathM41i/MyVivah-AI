<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AboutController extends Controller
{
    /**
     * GET /about — company / product story page (phase-5a §3.2).
     */
    public function __invoke(): View
    {
        return view('pages.about', [
            'meta' => [
                'title' => 'About',
                'description' => 'MyVivahAI builds API-first AI services for matrimony platforms — privacy-first, Hindi-and-English native, built for the South Asian wedding ecosystem.',
            ],
        ]);
    }
}
