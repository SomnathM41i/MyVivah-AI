<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * GET / — public marketing landing (phase-5a §3.1).
     */
    public function __invoke(): View
    {
        return view('pages.home', [
            'meta' => [
                'title' => 'AI & API Platform for Matrimony Websites',
                'description' => 'Add real-time chat, AI-powered services and automation to your matrimony website — with an API-first platform built for privacy and scale.',
            ],
        ]);
    }
}
