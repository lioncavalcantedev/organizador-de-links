@props([
    'title',
    'url',
    'image' => null,
    'tag' => null,
    'tagVariant' => 'blue',
    'editUrl' => null,
    'deleteAction' => null,
    'moveUrl' => null,
    'canMoveUp' => false,
    'canMoveDown' => false,
    'showDisabledActions' => false,
])

<div {{ $attributes->class('flex items-center gap-2') }} data-link-card>
    @if ($moveUrl)
        <div class="flex shrink-0 flex-col gap-1 sm:flex-row sm:gap-0" aria-label="Reordenar link">
            <form action="{{ $moveUrl }}" method="POST" data-reorder-form data-direction="up">
                @csrf
                @method('PATCH')
                <input type="hidden" name="direction" value="up">
                <x-icon-button
                    type="submit"
                    icon="arrow-up"
                    label="Mover {{ $title }} para cima"
                    :disabled="! $canMoveUp"
                    data-move-up
                />
            </form>

            <form action="{{ $moveUrl }}" method="POST" data-reorder-form data-direction="down">
                @csrf
                @method('PATCH')
                <input type="hidden" name="direction" value="down">
                <x-icon-button
                    type="submit"
                    icon="arrow-down"
                    label="Mover {{ $title }} para baixo"
                    :disabled="! $canMoveDown"
                    data-move-down
                />
            </form>
        </div>
    @endif

    <article class="flex min-w-0 flex-1 items-center gap-3 rounded-xl border border-border-primary bg-background-secondary p-3">
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
            @elseif ($showDisabledActions)
                <x-icon-button icon="trash" label="Excluir link (em breve)" disabled />
            @endif

            @if ($editUrl)
                <x-icon-button :href="$editUrl" icon="edit" label="Editar link" />
            @elseif ($showDisabledActions)
                <x-icon-button icon="edit" label="Editar link (em breve)" disabled />
            @endif
        </div>
    </article>
</div>
