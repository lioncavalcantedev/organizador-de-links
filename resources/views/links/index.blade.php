@extends('layouts.app')

@section('title', 'Links')

@section('content')
    <main class="min-h-screen px-5 py-8 pb-28 sm:px-8 sm:py-12">
        <x-logo class="mx-auto w-48" />

        <section class="mx-auto mt-12 w-full max-w-3xl">
            <div class="flex items-center justify-between gap-4">
                <h1 class="relative pb-3 text-heading-small text-content-primary after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-5 after:bg-accent-orange">
                    Links
                </h1>

                <x-button
                    variant="secondary"
                    icon="add"
                    disabled
                    preserve-disabled-opacity
                    aria-disabled="true"
                    title="Adicionar link (em breve)"
                >
                    Adicionar link
                </x-button>
            </div>

            <p id="reorder-status" class="sr-only" role="status" aria-live="polite"></p>
            <p id="reorder-error" class="mt-4 hidden text-paragraph-small text-accent-red" role="alert"></p>

            @if ($links->isEmpty())
                <div class="mt-8 rounded-xl border border-border-primary bg-background-secondary p-6 text-center text-paragraph-medium text-content-secondary">
                    Você ainda não adicionou nenhum link.
                </div>
            @else
                <ol id="links-list" class="mt-8 space-y-4">
                    @foreach ($links as $link)
                        <li data-link-item data-link-id="{{ $link->id }}">
                            <x-link-card
                                :title="$link->title"
                                :url="$link->url"
                                :image="$link->image_url"
                                :tag="$link->category"
                                :tag-variant="$link->category_variant"
                                :move-url="route('links.position.update', $link)"
                                :can-move-up="! $loop->first"
                                :can-move-down="! $loop->last"
                                :show-disabled-actions="true"
                            />
                        </li>
                    @endforeach
                </ol>
            @endif
        </section>

        <x-float-bar class="fixed bottom-6 left-1/2 -translate-x-1/2">
            <x-icon-button
                icon="list"
                variant="primary"
                label="Lista de links"
                disabled
                preserve-disabled-opacity
                aria-current="page"
            />
            <x-icon-button icon="user" label="Perfil (em breve)" disabled preserve-disabled-opacity />
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <x-icon-button type="submit" icon="logout" label="Sair" />
            </form>
        </x-float-bar>
    </main>
@endsection
