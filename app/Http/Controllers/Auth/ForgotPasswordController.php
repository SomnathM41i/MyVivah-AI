<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\ForgotPasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    /**
     * GET /forgot-password — request-a-reset email page.
     */
    public function showLinkRequestForm(): View
    {
        return view('auth.forgot-password', [
            'title' => 'Forgot your password?',
            'subtitle' => 'Enter the email you signed up with and we\'ll send you a reset link.',
        ]);
    }

    /**
     * POST /forgot-password — send a reset link via the `resets` broker
     * (throttle at route). Always returns the neutral status message to avoid
     * leaking whether an account exists (no account enumeration).
     */
    public function sendResetLink(ForgotPasswordRequest $request): RedirectResponse
    {
        $status = Password::broker()->sendResetLink([
            'email' => strtolower(trim($request->validated('email'))),
        ]);

        return back()
            ->with('status', $status === Password::RESET_LINK_SENT
                ? 'Reset link sent. Check your inbox (and spam folder).'
                : 'If that email is registered with us, we\'ve emailed you a reset link.');
    }
}
