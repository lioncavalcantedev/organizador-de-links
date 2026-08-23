@props([
    'name',
    'strokeWidth' => '1.75',
])

<svg
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="{{ $strokeWidth }}"
    stroke-linecap="round"
    stroke-linejoin="round"
    aria-hidden="true"
    {{ $attributes }}
>
    @switch($name)
        @case('trash')
            <path d="M4 7h16M9 11v6m6-6v6M6 7l1 14h10l1-14M9 7V4h6v3" />
            @break
        @case('edit')
            <path d="m4 20 4.5-1 10-10a2.12 2.12 0 0 0-3-3l-10 10L4 20Zm10-12 3 3" />
            @break
        @case('add')
            <circle cx="12" cy="12" r="9" />
            <path d="M12 8v8m-4-4h8" />
            @break
        @case('logout')
            <path d="M14 8V5H5v14h9v-3m-4-4h10m-3-3 3 3-3 3" />
            @break
        @case('user')
            <circle cx="12" cy="12" r="9" />
            <circle cx="12" cy="9" r="3" />
            <path d="M6.5 18c1.2-2.5 3-3.5 5.5-3.5s4.3 1 5.5 3.5" />
            @break
        @case('list')
            <path d="M9 6h10M9 12h10M9 18h10" />
            <path d="M5 6h.01M5 12h.01M5 18h.01" />
            @break
        @case('arrow-up')
            <path d="m5 11 7-7 7 7M12 4v16" />
            @break
        @case('arrow-down')
            <path d="m5 13 7 7 7-7M12 20V4" />
            @break
        @case('close')
            <circle cx="12" cy="12" r="9" fill="currentColor" stroke="none" />
            <path d="m9 9 6 6m0-6-6 6" class="text-content-inverse" />
            @break
        @case('check')
            <circle cx="12" cy="12" r="9" fill="currentColor" stroke="none" />
            <path d="m8 12 2.5 2.5L16 9" class="text-content-inverse" />
            @break
        @default
            <path d="M5 7h14M5 12h14M5 17h14" />
    @endswitch
</svg>
