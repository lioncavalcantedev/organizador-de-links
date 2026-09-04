@extends('layouts.app')

@section('title', 'Perfil')

@section('content')
    @php($imageUrl = $user->image_url ? Storage::disk('public')->url($user->image_url) : null)

    <main class="min-h-screen px-5 py-8 pb-28 sm:px-8 sm:py-12">
        <x-logo class="mx-auto w-48" />

        <section class="mx-auto mt-12 w-full max-w-3xl">
            <div class="flex items-center justify-between gap-4">
                <h1 class="relative pb-3 text-heading-small text-content-primary after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-5 after:bg-accent-orange">
                    Perfil
                </h1>

                <div class="flex items-center gap-4">
                    <x-button :href="route('links.index')" variant="secondary">Voltar</x-button>
                    <x-button type="submit" form="profile-form" data-profile-submit>Salvar</x-button>
                </div>
            </div>

            <form id="profile-form" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="mt-12" data-profile-form>
                @csrf
                @method('PATCH')

                <p class="mb-6 hidden rounded-lg border border-accent-green/30 bg-accent-green/10 px-4 py-3 text-paragraph-medium text-accent-green" role="status" aria-live="polite" data-profile-status></p>
                <p class="mb-6 hidden rounded-lg border border-accent-red/30 bg-accent-red/10 px-4 py-3 text-paragraph-medium text-accent-red" role="alert" data-profile-error></p>

                <div class="grid gap-8 sm:grid-cols-[minmax(0,477px)_auto] sm:items-start sm:gap-12">
                    <div class="space-y-6">
                        <div>
                            <x-input label="Nome" name="name" :value="$user->name" required data-profile-field />
                            <p class="mt-2 hidden text-paragraph-small text-accent-red" data-profile-field-error="name"></p>
                        </div>

                        <div>
                            <x-input label="E-mail" name="email" type="email" :value="$user->email" required data-profile-field />
                            <p class="mt-2 hidden text-paragraph-small text-accent-red" data-profile-field-error="email"></p>
                        </div>

                        <div>
                            <x-textarea label="Bio" name="bio" :value="$user->bio" rows="4" maxlength="500" required data-profile-field />
                            <p class="mt-2 hidden text-paragraph-small text-accent-red" data-profile-field-error="bio"></p>
                        </div>
                    </div>

                    <div>
                        <div class="size-[100px] overflow-hidden rounded-xl bg-background-secondary">
                            <img
                                src="{{ $imageUrl }}"
                                alt="Foto de perfil de {{ $user->name }}"
                                class="size-full object-cover"
                                data-profile-image-preview
                                @if (! $imageUrl) hidden @endif
                            >
                            <div class="flex size-full items-center justify-center text-content-tertiary" data-profile-image-placeholder @if ($imageUrl) hidden @endif>
                                <x-icon name="user" class="size-10" />
                            </div>
                        </div>

                        <input
                            id="profile-image"
                            name="image"
                            type="file"
                            class="sr-only"
                            accept="image/jpeg,image/png,image/webp"
                            data-profile-image-input
                        >

                        <label for="profile-image" class="mt-3 inline-flex cursor-pointer items-center gap-2 text-label-small text-content-primary hover:text-accent-orange">
                            <x-icon name="upload" class="size-4" />
                            Substituir imagem
                        </label>
                        <p class="mt-2 hidden text-paragraph-small text-accent-red" data-profile-field-error="image"></p>
                    </div>
                </div>
            </form>
        </section>

        <x-float-bar class="fixed bottom-6 left-1/2 -translate-x-1/2">
            <x-icon-button icon="list" label="Lista de links" :href="route('links.index')" />
            <x-icon-button icon="user" variant="primary" label="Perfil" disabled preserve-disabled-opacity aria-current="page" />
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <x-icon-button type="submit" icon="logout" label="Sair" />
            </form>
        </x-float-bar>
    </main>
@endsection
