@props([
    'label',
    'name',
    'value' => null,
    'error' => null,
    'rows' => 4,
])

@php
    $textareaId = $attributes->get('id', $name);
    $errorMessage = $error ?? $errors->first($name);
    $describedBy = $errorMessage ? $textareaId.'-error' : null;
@endphp

<div class="space-y-2">
    <label for="{{ $textareaId }}" class="block text-label-small text-content-primary">
        {{ $label }}
    </label>

    <textarea
        name="{{ $name }}"
        id="{{ $textareaId }}"
        rows="{{ $rows }}"
        @if ($describedBy) aria-describedby="{{ $describedBy }}" aria-invalid="true" @endif
        {{ $attributes
            ->except(['id'])
            ->class([
                'w-full resize-y rounded-lg border bg-background-secondary px-3 py-2.5 text-paragraph-medium text-content-primary',
                'placeholder:text-content-tertiary transition-colors hover:bg-background-tertiary focus:outline-none',
                'border-border-primary focus:border-content-tertiary' => ! $errorMessage,
                'border-accent-red focus:border-accent-red' => $errorMessage,
            ]) }}
    >{{ old($name, $value) }}</textarea>

    @if ($errorMessage)
        <p id="{{ $textareaId }}-error" class="flex items-center gap-1.5 text-paragraph-small text-accent-red">
            <span class="size-1.5 shrink-0 rounded-full bg-current" aria-hidden="true"></span>
            {{ $errorMessage }}
        </p>
    @endif
</div>
