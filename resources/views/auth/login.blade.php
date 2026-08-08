@extends('layouts.app')

@section('title', 'Acessar conta')

@section('content')
    <x-auth-layout title="Acessar conta">
        @if (session('success'))
            <div
                role="status"
                class="mb-6 rounded-lg border border-accent-green bg-background-secondary p-3 text-paragraph-small text-accent-green"
            >
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-5">
            @csrf

            <x-input
                label="E-mail"
                name="email"
                type="email"
                autocomplete="email"
                placeholder="Digite seu e-mail"
                required
                autofocus
            />

            <x-input
                label="Senha"
                name="password"
                type="password"
                autocomplete="current-password"
                placeholder="Insira sua senha"
                required
            />

            <div class="flex justify-center pt-10">
                <x-button type="submit" class="w-44">
                    Acessar conta
                </x-button>
            </div>
        </form>

        <x-slot:footer>
            <p>
                Não tem cadastro?
                <a
                    href="{{ route('register') }}"
                    class="font-semibold text-content-primary transition-colors hover:text-accent-orange"
                >
                    Criar conta
                </a>
            </p>
        </x-slot:footer>
    </x-auth-layout>
@endsection
