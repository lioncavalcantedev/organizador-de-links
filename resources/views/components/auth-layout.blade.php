@props(['title'])

<main class="min-h-screen p-5 lg:grid lg:grid-cols-[minmax(0,1.17fr)_minmax(32rem,1fr)] lg:gap-5">
    <div class="hidden min-h-[calc(100vh-2.5rem)] overflow-hidden rounded-2xl lg:block">
        <img
            src="{{ asset('images/ilustracao.png') }}"
            alt=""
            class="size-full object-cover"
        >
    </div>

    <section class="mx-auto flex min-h-[calc(100vh-2.5rem)] w-full max-w-md flex-col px-1 py-8 sm:px-6 lg:max-w-none lg:px-12 lg:py-16">
        <x-logo class="mx-auto w-48 shrink-0" />

        <div class="my-auto w-full">
            <div class="mb-10 flex items-end gap-4">
                <span class="h-px flex-1 bg-accent-orange" aria-hidden="true"></span>
                <h1 class="text-heading-small text-content-primary">{{ $title }}</h1>
                <span class="h-px flex-1 bg-accent-orange" aria-hidden="true"></span>
            </div>

            {{ $slot }}
        </div>

        @isset($footer)
            <div class="mt-10 text-center text-paragraph-small text-content-secondary">
                {{ $footer }}
            </div>
        @endisset
    </section>
</main>
