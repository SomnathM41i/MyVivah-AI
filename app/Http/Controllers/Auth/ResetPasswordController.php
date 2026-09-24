<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\ResetPasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ResetPasswordController extends Controller
{
    /**
     * GET /reset-password/{token} — password reset form (token from email).
     */
    public function showResetForm(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'title' => 'Set a new password',
            'subtitle' => 'Choose a strong password to continue to your account.',
            'token' => $token,
            'email' => (string) $request->query('email', ''),
        ]);
    }

    /**
     * POST /reset-password — consume the token and rotate the password
     * (throttle at route). Token validity + throttle enforced by the broker.
     */
    public function store(ResetPasswordRequest $request): RedirectResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password): void {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Your password has been reset. You can now sign in.')
            : back()->withErrors(['email' => __($status)]);
    }
}
