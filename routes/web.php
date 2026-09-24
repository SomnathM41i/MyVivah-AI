<?php

use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Demo\ChatDemoController;
use App\Http\Controllers\Public\AboutController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\PlansController;
use App\Http\Controllers\Public\ServicesController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Phase 5A — public marketing site + full web authentication + an authenticated
| app shell. The API lives under /api/v1 (routes/api.php) and is untouched here;
| session auth is the separate `web` guard. `/` changed from the scaffold
| `welcome` view to the real marketing home.
|
*/

// --- Public marketing pages (indexable) -------------------------------------
Route::get('/', HomeController::class)->name('home');
Route::get('/about', AboutController::class)->name('about');
Route::get('/services', ServicesController::class)->name('services.index');
Route::get('/plans', PlansController::class)->name('plans.index');

Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('contact.store');

// --- Guest-only auth screens -------------------------------------------------
Route::middleware('guest')->group(function (): void {
    Route::get('/signup', [RegisteredUserController::class, 'create'])->name('signup');
    Route::post('/signup', [RegisteredUserController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('signup.store');

    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('login.store');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])
        ->middleware('throttle:5,1')
        ->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('password.store');
});

// Public "check your inbox" notice (verification is a signed URL — the link
// itself is the credential, so no login is required to verify). Kept OUTSIDE
// the guest group so the `verified` middleware can redirect an authenticated-
// but-unverified user here too.
Route::get('/verify-email', [VerifyEmailController::class, 'notice'])->name('verification.notice');

// Email verification link + resend (the resend accepts any email; both are
// throttled + neutral-messaged so neither endpoint enumerates accounts).
Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, 'verify'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/verify-email/resend', [EmailVerificationNotificationController::class, 'store'])
    ->middleware('throttle:3,1')
    ->name('verification.send');

Route::post('/logout', LogoutController::class)
    ->middleware('auth')
    ->name('logout');

// --- Authenticated app shell (Phase 5A foundation) ---------------------------
Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/services', [DashboardController::class, 'services'])->name('dashboard.services');
    Route::get('/dashboard/integrations', [DashboardController::class, 'integrations'])->name('dashboard.integrations');
    Route::get('/dashboard/subscription', [DashboardController::class, 'subscription'])->name('dashboard.subscription');
    Route::get('/dashboard/payments', [DashboardController::class, 'payments'])->name('dashboard.payments');
    Route::get('/dashboard/settings', [DashboardController::class, 'settings'])->name('dashboard.settings');
});

// --- Local-only widget demo (Phase 4, unchanged) -----------------------------
Route::get('/demo/chat', ChatDemoController::class)->name('demo.chat');
