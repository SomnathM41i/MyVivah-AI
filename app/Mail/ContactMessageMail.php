<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Phase 5A — deliver a public contact submission to the support inbox
 * (`config('mail.to.address')`). Rendered from the framework email layout.
 */
class ContactMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public ContactMessage $contact) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            replyTo: [new Address($this->contact->email, $this->contact->name)],
            subject: "New contact message — {$this->contact->name}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        /** @var view-string $view */
        $view = 'emails.contact-message';

        return new Content(markdown: $view);
    }
}
