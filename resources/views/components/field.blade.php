@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'value' => '',
    'required' => false,
    'autocomplete' => null,
    'placeholder' => null,
    'hint' => null,
    'inputmode' => null,
    'autofocus' => false,
    'trailing' => null,
])

<div {{ $attributes->only(['class'])->class(['space-y-1.5']) }}>
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-ink-soft">
            {{ $label }}
            @if ($required)
                <span class="text-brand-600">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        <input
            id="{{ $name }}"
            name="{{ $name }}"
            type="{{ $type }}"
            @if ($type !== 'password') value="{{ old($name, $value) }}" @endif
            @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
            @if ($placeholder) placeholder="{{ $placeholder }}" @endif
            @if ($inputmode) inputmode="{{ $inputmode }}" @endif
            @if ($required) required @endif
            @if ($autofocus) autofocus @endif
            @error($name)
                aria-invalid="true"
                aria-describedby="{{ $name }}-error"
            @enderror
            {{ $attributes->whereDoesntStartWith('class', 'value')->whereStartsWith('form') }}
        class="block w-full rounded-xl border border-ink/10 bg-white px-3.5 py-2.5 text-sm text-ink shadow-sm placeholder:text-ink/35 focus-ring disabled:cursor-not-allowed disabled:bg-ink/5 @if ($trailing) pr-11 @endif @error($name) border-brand-500 @enderror"
        />

        @if ($trailing)
            <div class="absolute inset-y-0 right-0 flex items-center pr-3 text-ink/45">
                {{ $trailing }}
            </div>
        @endif
    </div>

    @if ($hint)
        <p class="text-xs text-ink/50">{{ $hint }}</p>
    @endif

    @error($name)
        <p id="{{ $name }}-error" class="text-xs font-medium text-brand-600">{{ $message }}</p>
    @enderror
</div>