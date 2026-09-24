<?php

namespace App\Notifications;

use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

/**
 * Phase 5A — email verification (registration-flow.md §2).
 *
 * Sends a time-limited, signed `verification.verify` URL. Sent immediately after
 * registration; the login guard refuses unverified accounts until the URL is
 * visited (VerifyEmailController marks `email_verified_at` + signs the user in).
 */
class VerifyEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $platformName = '') {}

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
        return (new MailMessage)
            ->subject('Verify your email — MyVivahAI')
            ->greeting("Hi {$notifiable->name},")
            ->line('Thanks for creating your MyVivahAI account. One quick step left:')
            ->line('Click the button below to confirm your email address and activate your account.')
            ->action('Verify email address', $this->verificationUrl($notifiable))
            ->line('This link expires in 60 minutes. If you did not create an account, you can safely ignore this email.')
            ->salutation('— The MyVivahAI Team');
    }

    /**
     * Time-limited, signed URL for this notifiable.
     */
    protected function verificationUrl(object $notifiable): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes((int) config('auth.verify.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ],
        );
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'url' => $this->verificationUrl($notifiable),
            'expires_at' => now()->addMinutes((int) config('auth.verify.expire', 60))->format(CarbonInterface::ATOM),
        ];
    }
}
