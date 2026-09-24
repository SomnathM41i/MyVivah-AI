<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * GET /login — render the sign-in form (guest middleware at the route).
     */
    public function create(): View
    {
        return view('auth.login', [
            'title' => 'Welcome back',
            'subtitle' => 'Sign in to manage your platform, subscriptions and integrations.',
        ]);
    }

    /**
     * POST /login — authenticate a verified, active account (throttle at route).
     *
     * Registration-flow gating:
     *   1. Credentials match a real account (constant message, no enumeration).
     *   2. Account is not suspended/deactivated.
     *   3. Email is verified (unverified → verification notice with resend).
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        $user = User::query()
            ->where('email', strtolower(trim((string) $request->validated('email'))))
            ->first();

        if ($user === null || ! Auth::validate($credentials)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        if (in_array($user->status, ['suspended', 'deactivated'], true)) {
            throw ValidationException::withMessages([
                'email' => 'This account is not active. Please contact support@myvivahai.in for help.',
            ]);
        }

        if (! $user->hasVerifiedEmail()) {
            return redirect()
                ->route('verification.notice')
                ->with('registration_email', $user->email)
                ->with('status', 'Please verify your email address before signing in.');
        }

        Auth::login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        $user->update(['last_login_at' => now()]);

        return redirect()
            ->intended(route('dashboard'))
            ->with('success', 'Welcome back, '.$user->name.'.');
    }
}
