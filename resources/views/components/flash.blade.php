@if (session('status'))
    <x-alert tone="info" data-autohide="5000">{{ session('status') }}</x-alert>
@endif

@if (session('success'))
    <x-alert tone="success" data-autohide="6000">{{ session('success') }}</x-alert>
@endif

@if (session('error'))
    <x-alert tone="error" data-autohide="8000">{{ session('error') }}</x-alert>
@endif

@if (session('warning'))
    <x-alert tone="warning" data-autohide="8000">{{ session('warning') }}</x-alert>
@endif

@if ($errors->any())
    <x-alert tone="error" :dismissible="false">
        <strong class="font-semibold">Please fix the following:</strong>
        <ul class="mt-2 list-inside list-disc space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </x-alert>
@endif