<?php

namespace Tests\Feature;

use App\Mail\ContactMessageMail;
use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Phase 5A — public contact form: stores a privacy-minimal submission and
 * notifies the support inbox. Honeypot blocks bots; nothing is enumerated.
 */
class ContactSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_stores_the_message_and_emails_support(): void
    {
        Mail::fake();

        $this->post('/contact', [
            'name' => 'Asha Sharma',
            'company' => 'Namma Matrimony',
            'email' => 'asha@namma.example',
            'phone' => '+919876543210',
            'message' => 'We want to add real-time chat to our platform for 50,000 members.',
        ])->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Asha Sharma',
            'company' => 'Namma Matrimony',
            'email' => 'asha@namma.example',
            'phone' => '+919876543210',
        ]);

        Mail::assertSent(ContactMessageMail::class, fn (ContactMessageMail $mail) => $mail->hasTo(config('mail.to.address')));

        $message = ContactMessage::query()->firstOrFail();
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', (string) $message->ip_hash);
        $this->assertStringNotContainsString('127.0.0.1', (string) $message->ip_hash);
    }

    public function test_contact_form_validates_required_fields(): void
    {
        $this->post('/contact', [])
            ->assertSessionHasErrors(['name', 'email', 'message']);

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_contact_form_honeypot_traps_bots(): void
    {
        // Bots autofill the hidden honeypot field — the submission is discarded.
        $this->post('/contact', [
            'name' => 'Spam Bot',
            'email' => 'bot@spam.example',
            'message' => 'buy cheap followers now',
            'website' => 'http://spam.example',
        ])->assertSessionHasErrors('website');

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_contact_submission_stores_only_a_hashed_ip(): void
    {
        $this->post('/contact', [
            'name' => 'Pranav Rao',
            'email' => 'pranav@example.test',
            'message' => 'We would like an enterprise plan proposal.',
        ]);

        $this->assertSame(
            hash('sha256', '127.0.0.1'),
            ContactMessage::query()->orderByDesc('id')->value('ip_hash'),
        );
        // The raw IP must never be persisted.
        $this->assertDatabaseMissing('contact_messages', ['ip_hash' => '127.0.0.1']);
    }
}
