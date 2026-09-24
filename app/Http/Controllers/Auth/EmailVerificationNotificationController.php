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
            $user->notify(new VerifyEmailNotification($user->platforms()->latest('id')->value('name') ?? ''));
        }

        return back()->with('status', 'If that email needs verification, we\'ve sent a fresh link.');
    }
}
