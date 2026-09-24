<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Phase 5A — password reset (Laravel `resets` broker + `password.reset` route).
 *
 * Dispatched via `User::sendPasswordResetNotification($token)` using the
 * framework `Password` broker, so the token expiry + throttle rules from
 * `config/auth.php` (`auth.passwords.users.expire`) apply unchanged.
 */
class ResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $token) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $expires = (int) config('auth.passwords.users.expire', 60);

        $resetUrl = $this->resetUrl($notifiable);

        return (new MailMessage)
            ->subject('Reset your password — MyVivahAI')
            ->greeting("Hi {$notifiable->name},")
            ->line('We received a request to reset your MyVivahAI password.')
            ->line('Click the button below to choose a new password.')
            ->action('Reset password', $resetUrl)
            ->line("This link expires in {$expires} minutes and can only be used once.")
            ->line('If you did not request a password reset, no further action is needed — your password stays the same.')
            ->salutation('— The MyVivahAI Team');
    }

    /**
     * The signed-ish reset URL landing on the `password.reset` form (token-based,
     * not signed — the broker validates the token against the user's hashes).
     */
    protected function resetUrl(object $notifiable): string
    {
        return url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'reset_url' => $this->resetUrl($notifiable),
            'expires_minutes' => (int) config('auth.passwords.users.expire', 60),
        ];
    }
}
