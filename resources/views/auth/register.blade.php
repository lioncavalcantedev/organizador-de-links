@extends('layouts.app')

@section('title', 'Criar conta')

@section('content')
    <x-auth-layout title="Criar conta">
        @error('register')
            <div
                role="alert"
                class="mb-6 rounded-lg border border-accent-red bg-background-secondary p-3 text-paragraph-small text-accent-red"
            >
                {{ $message }}
            </div>
        @enderror

        <form action="{{ route('register') }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid gap-5 sm:grid-cols-2">
                <x-input
                    label="Nome"
                    name="first_name"
                    autocomplete="given-name"
                    placeholder="Seu nome"
                    required
                    autofocus
                />

                <x-input
                    label="Sobrenome"
                    name="last_name"
                    autocomplete="family-name"
                    placeholder="Seu sobrenome"
                    required
                />
            </div>

            <x-input
                label="E-mail"
                name="email"
                type="email"
                autocomplete="email"
                placeholder="seuemail@exemplo.com"
                required
            />

            <x-input
                label="Senha"
                name="password"
                type="password"
                autocomplete="new-password"
                placeholder="Mínimo de 8 caracteres"
                required
            />

            <div class="flex justify-center pt-10">
                <x-button type="submit" class="w-44">
                    Criar conta
                </x-button>
            </div>
        </form>

        <x-slot:footer>
            <p>
                Já tem cadastro?
                <a
                    href="{{ route('login') }}"
                    class="font-semibold text-content-primary transition-colors hover:text-accent-orange"
                >
                    Acessar conta
                </a>
            </p>
        </x-slot:footer>
    </x-auth-layout>
@endsection
