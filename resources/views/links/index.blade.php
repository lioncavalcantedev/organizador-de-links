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
                    id="add-link-button"
                    aria-haspopup="dialog"
                    aria-controls="add-link-modal"
                    data-modal-open
                >
                    Adicionar link
                </x-button>
            </div>

            @if (session('message'))
                <p class="mt-4 rounded-lg border border-accent-green/30 bg-accent-green/10 px-4 py-3 text-paragraph-medium text-accent-green" role="status">
                    {{ session('message') }}
                </p>
            @endif

            <p id="reorder-status" class="sr-only" role="status" aria-live="polite"></p>
            <p id="reorder-error" class="mt-4 hidden text-paragraph-medium text-accent-red" role="alert"></p>

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
            <x-icon-button icon="user" label="Perfil" :href="route('profile.edit')" />
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <x-icon-button type="submit" icon="logout" label="Sair" />
            </form>
        </x-float-bar>
    </main>

    <div
        id="add-link-modal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-modal-backdrop p-5 backdrop-blur-lg"
        role="dialog"
        aria-modal="true"
        aria-labelledby="add-link-title"
        aria-hidden="true"
        data-modal
        hidden
    >
        <div class="w-full max-w-[632px] rounded-[20px] bg-modal-background p-6 shadow-2xl sm:p-8" data-modal-dialog tabindex="-1">
            <h2 id="add-link-title" class="relative text-heading-small text-content-primary after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-5 after:bg-accent-orange">
                Adicionar link
            </h2>

            <form action="{{ route('links.store') }}" method="POST" enctype="multipart/form-data" class="mt-8" data-link-form>
                @csrf

                <div class="sm:flex sm:items-start">
                    <div class="sm:w-[394px]">
                        <div class="grid gap-3 sm:grid-cols-[190px_192px]">
                            <x-input label="Título do link" name="title" placeholder="Digite o nome do conteúdo" variant="modal" required />

                            <x-input label="Plataforma de streaming" name="platform" placeholder="Onde você está assistindo?" variant="modal" required />
                        </div>

                        <div class="mt-7">
                            <x-input label="URL" name="url" type="url" placeholder="Cole a URL do conteúdo" variant="modal" required />
                        </div>
                    </div>

                    <div class="mt-6 sm:ml-[52px] sm:mt-3">
                        <label class="flex size-[100px] cursor-pointer items-center justify-center overflow-hidden rounded-xl bg-modal-accent text-center text-paragraph-small text-content-inverse transition hover:brightness-110" for="image">
                            <img id="image-preview" class="hidden size-full object-cover" alt="Prévia da imagem selecionada">
                            <span id="image-preview-placeholder">100×100 px</span>
                        </label>

                        <input
                            id="image"
                            name="image"
                            type="file"
                            class="sr-only"
                            accept="image/jpeg,image/png,image/webp"
                            required
                            @error('image') aria-describedby="image-error" aria-invalid="true" @enderror
                            data-image-input
                        >

                        <div class="mt-3">
                            <label for="image" class="inline-flex cursor-pointer items-center gap-2 whitespace-nowrap text-label-small text-content-primary hover:text-modal-accent">
                                <x-icon name="upload" class="size-4" />
                                Adicionar imagem
                            </label>
                        </div>

                        @error('image')
                            <p id="image-error" class="flex items-center gap-1.5 text-paragraph-small text-accent-red">
                                <span class="size-1.5 shrink-0 rounded-full bg-current" aria-hidden="true"></span>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="mt-10 flex justify-end gap-4">
                    <x-button variant="modal-secondary" type="button" data-modal-close>Voltar</x-button>
                    <x-button variant="modal-primary" type="submit">Salvar</x-button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.openAddLinkModal = {{ $errors->any() ? 'true' : 'false' }};
    </script>
@endpush
