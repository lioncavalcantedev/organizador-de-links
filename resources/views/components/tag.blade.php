@props(['variant' => 'blue'])

@php
    $classes = match ($variant) {
        'purple' => 'bg-accent-purple text-content-inverse',
        'green' => 'bg-accent-green text-content-inverse',
        default => 'bg-accent-blue text-content-inverse',
    };
@endphp

<span {{ $attributes->class(['inline-flex rounded-full px-2 py-1 text-label-small', $classes]) }}>
    {{ $slot }}
</span>
