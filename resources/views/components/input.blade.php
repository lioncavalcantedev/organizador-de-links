@props([
    'label',
    'name',
    'type' => 'text',
    'value' => null,
    'error' => null,
    'variant' => 'default',
])

@php
    $inputId = $attributes->get('id', $name);
    $errorMessage = $error ?? $errors->first($name);
    $describedBy = $errorMessage ? $inputId.'-error' : null;
@endphp

<div class="space-y-2">
    <label for="{{ $inputId }}" class="block text-label-small text-content-primary">
        {{ $label }}
    </label>

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $inputId }}"
        value="{{ $type !== 'password' ? old($name, $value) : null }}"
        @if ($describedBy) aria-describedby="{{ $describedBy }}" aria-invalid="true" @endif
        {{ $attributes
            ->except(['id'])
            ->class([
                'w-full rounded-lg border bg-background-secondary px-3 py-2.5 text-paragraph-medium text-content-primary',
                'placeholder:text-content-tertiary transition-colors hover:bg-background-tertiary focus:outline-none',
                'border-transparent bg-background-secondary placeholder:text-paragraph-small hover:bg-background-tertiary focus:border-modal-accent' => $variant === 'modal' && ! $errorMessage,
                'border-border-primary focus:border-content-tertiary' => $variant !== 'modal' && ! $errorMessage,
                'border-accent-red focus:border-accent-red' => $errorMessage,
            ]) }}
    >

    @if ($errorMessage)
        <p id="{{ $inputId }}-error" class="flex items-center gap-1.5 text-paragraph-small text-accent-red">
            <span class="size-1.5 shrink-0 rounded-full bg-current" aria-hidden="true"></span>
            {{ $errorMessage }}
        </p>
    @endif
</div>
