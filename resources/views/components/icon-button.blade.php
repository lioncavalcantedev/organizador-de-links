@props([
    'label',
    'icon',
    'variant' => 'tertiary',
    'type' => 'button',
    'href' => null,
])

@php
    $classes = match ($variant) {
        'primary' => 'bg-accent-orange text-content-inverse hover:brightness-110',
        'secondary' => 'bg-background-secondary text-accent-orange hover:bg-background-tertiary hover:text-content-primary',
        default => 'bg-transparent text-content-primary hover:bg-background-secondary',
    };

    $tag = $href ? 'a' : 'button';
@endphp

<{{ $tag }}
    @if ($href) href="{{ $href }}" @else type="{{ $type }}" @endif
    aria-label="{{ $label }}"
    title="{{ $label }}"
    {{ $attributes->class([
        'inline-flex size-10 shrink-0 items-center justify-center rounded-full transition',
        'enabled:cursor-pointer disabled:cursor-not-allowed disabled:opacity-50',
        $classes,
    ]) }}
>
    <x-icon :name="$icon" class="size-4" />
</{{ $tag }}>
