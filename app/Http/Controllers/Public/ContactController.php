<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\ContactRequest;
use App\Mail\ContactMessageMail;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    /**
     * GET /contact — public contact + support page (phase-5a §3.5).
     */
    public function index(): View
    {
        return view('pages.contact', [
            'meta' => [
                'title' => 'Contact',
                'description' => 'Talk to the MyVivahAI team about adding AI-powered services to your matrimony platform.',
            ],
        ]);
    }

    /**
     * POST /contact — store the submission and notify the support inbox.
     * Privacy-minimal: only a SHA-256 hash of the submitter IP is stored.
     */
    public function store(ContactRequest $request): RedirectResponse
    {
        $contact = ContactMessage::query()->create([
            'name' => trim($request->validated('name')),
            'company' => $request->validated('company') !== null
                ? trim((string) $request->validated('company'))
                : null,
            'email' => strtolower(trim($request->validated('email'))),
            'phone' => $request->validated('phone') !== null
                ? trim((string) $request->validated('phone'))
                : null,
            'message' => trim($request->validated('message')),
            'ip_hash' => hash('sha256', $request->ip() ?? ''),
            'source_url' => str($request->headers->get('referer', ''))->limit(2048)->toString() ?: null,
        ]);

        if (config('mail.to.address') !== null) {
            Mail::to(config('mail.to.address'))->send(new ContactMessageMail($contact));
        }

        return back()->with('success', "Thank you {$contact->name} — we've received your message and will reply to {$contact->email} soon.");
    }
}
