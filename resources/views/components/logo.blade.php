@props(['alt' => config('app.name', 'Sitemark')])

@if (file_exists(public_path('images/logo.svg')))
    <img
        src="{{ asset('images/logo.svg') }}"
        alt="{{ $alt }}"
        {{ $attributes->class('h-auto max-w-full') }}
    >
@else
    <span
        role="img"
        aria-label="{{ $alt }}"
        {{ $attributes->class('inline-flex items-center gap-2 text-heading-small text-content-primary') }}
    >
        <span class="text-accent-orange" aria-hidden="true">✦</span>
        <span>{{ $alt }}</span>
    </span>
@endif
