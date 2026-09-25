<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\RegisterRequest;
use App\Notifications\VerifyEmailNotification;
use App\Services\PlatformAccountService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * GET /signup — self-service platform registration form (phase-5a §4).
     */
    public function create(): View
    {
        return view('auth.signup', [
            'title' => 'Create your account',
            'subtitle' => 'Register your platform and add AI-powered services to your matrimony website.',
        ]);
    }

    /**
     * POST /signup — register the owner + first platform transactionally
     * (throttle at the route). Email verification is required before the first
     * sign-in, so the success path lands on the verification notice page.
     */
    public function store(
        RegisterRequest $request,
        PlatformAccountService $accounts,
    ): RedirectResponse {
        [$user, $platform] = $accounts->register($request->validated());

        try {
            $user->notify(new VerifyEmailNotification($platform->name));
            $status = 'Account created! One last step — we sent a verification link to '.$user->email.'.';
        } catch (\Throwable $exception) {
            report($exception);
            $status = 'Your account was created, but we could not send the verification email right now. Use the resend form below to try again.';
        }

        return redirect()
            ->route('verification.notice')
            ->with('registration_email', $user->email)
            ->with('status', $status);
    }
}
