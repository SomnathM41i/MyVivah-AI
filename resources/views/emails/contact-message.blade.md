<x-mail::message>
# New contact message

A new message was submitted through the public contact form on **{{ $contact->created_at?->format('D, j M Y, H:i') ?: now()->format('D, j M Y, H:i') }}**.

## Submitted
@component('mail::table')
| Field | Value |
| --- | --- |
| **Name** | {{ $contact->name }} |
| **Company** | {{ $contact->company ?: '—' }} |
| **Email** | [{{ $contact->email }}](mailto:{{ $contact->email }}) |
| **Phone** | {{ $contact->phone ?: '—' }} |
| **Source page** | {{ $contact->source_url ?: '—' }} |
@endcomponent

## Message
> {{ $contact->message }}

<x-mail::button :url="'mailto:'.$contact->email">
Reply to {{ $contact->name }}
</x-mail::button>

<x-mail::subcopy>
Reply directly from this email — replies go to {{ $contact->email }}.
</x-mail::subcopy>
</x-mail::message>