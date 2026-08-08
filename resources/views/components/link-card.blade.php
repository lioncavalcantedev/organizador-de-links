@props([
    'title',
    'url',
    'image' => null,
    'tag' => null,
    'tagVariant' => 'blue',
    'editUrl' => null,
    'deleteAction' => null,
])

<article {{ $attributes->class('flex items-center gap-3 rounded-xl border border-border-primary bg-background-secondary p-3') }}>
    <div class="size-12 shrink-0 overflow-hidden rounded-lg bg-content-primary">
        @if ($image)
            <img src="{{ $image }}" alt="" class="size-full object-cover">
        @endif
    </div>

    <div class="min-w-0 flex-1">
        <div class="flex items-center gap-2">
            <h2 class="truncate text-label-medium text-content-primary">{{ $title }}</h2>

            @if ($tag)
                <x-tag :variant="$tagVariant">{{ $tag }}</x-tag>
            @endif
        </div>

        <a
            href="{{ $url }}"
            class="block truncate text-paragraph-small text-content-secondary hover:text-content-primary"
            target="_blank"
            rel="noopener noreferrer"
        >
            {{ preg_replace('#^https?://#', '', $url) }}
        </a>
    </div>

    <div class="flex shrink-0 items-center gap-1">
        @if ($deleteAction)
            <form action="{{ $deleteAction }}" method="POST">
                @csrf
                @method('DELETE')
                <x-icon-button type="submit" icon="trash" label="Excluir link" />
            </form>
        @endif

        @if ($editUrl)
            <x-icon-button :href="$editUrl" icon="edit" label="Editar link" />
        @endif
    </div>
</article>
