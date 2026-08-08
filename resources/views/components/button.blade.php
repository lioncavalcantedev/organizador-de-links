@props([
    'variant' => 'primary',
    'type' => 'button',
    'href' => null,
    'icon' => null,
])

@php
    $classes = match ($variant) {
        'secondary' => 'bg-background-secondary text-accent-orange hover:bg-background-tertiary hover:text-content-primary',
        default => 'bg-accent-orange text-content-inverse hover:brightness-110',
    };

    $tag = $href ? 'a' : 'button';
@endphp

<{{ $tag }}
    @if ($href) href="{{ $href }}" @else type="{{ $type }}" @endif
    {{ $attributes->class([
        'inline-flex min-h-10 items-center justify-center gap-2 rounded-full px-4 text-label-medium',
        'transition enabled:cursor-pointer disabled:cursor-not-allowed disabled:opacity-50',
        $classes,
    ]) }}
>
    @if ($icon)
        <x-icon :name="$icon" class="size-4" />
    @endif

    {{ $slot }}
</{{ $tag }}>
