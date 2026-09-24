<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VerifyEmailController extends Controller
{
    /**
     * GET /verify-email — public "check your inbox" notice with a resend form
     * (guest accessible; reached after signup and after an unverified sign-in).
     */
    public function notice(Request $request): View
    {
        return view('auth.verify-email', [
            'title' => 'Verify your email',
            'subtitle' => 'We\'ve emailed you a verification link. Click it to activate your account.',
            'email' => (string) $request->session()->get('registration_email', ''),
        ]);
    }

    /**
     * GET /verify-email/{id}/{hash} — verify via the signed URL from the email
     * (`signed` middleware enforces expiry + tamper-proof signature). No login
     * is required to verify: the URL itself is the credential. Marks verified,
     * signs the user in, and lands on the dashboard.
     */
    public function verify(Request $request, string $id, string $hash): RedirectResponse
    {
        $user = User::query()->find($id);

        if ($user === null || ! hash_equals(sha1($user->getEmailForVerification()), (string) $hash)) {
            abort(403, 'This verification link is invalid.');
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        Auth::login($user, false);
        $request->session()->regenerate();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Email verified. Welcome to MyVivahAI!');
    }
}
