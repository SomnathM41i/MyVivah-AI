<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * POST /verify-email/resend — resend the verification email for an
     * unverified account (throttle at route). Always answers with the same
     * neutral message so this endpoint cannot be used to enumerate accounts.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'string', 'email:rfc', 'max:255'],
        ]);

        $user = User::query()
            ->where('email', strtolower(trim($data['email'])))
            ->whereNull('email_verified_at')
            ->first();

        if ($user !== null) {
            try {
                $user->notify(new VerifyEmailNotification($user->platforms()->latest('id')->value('name') ?? ''));
            } catch (\Throwable $exception) {
                report($exception);
            }
        }

        return back()->with('status', 'If that email belongs to an unverified account, a fresh verification link has been requested.');
    }
}
