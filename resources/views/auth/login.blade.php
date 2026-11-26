@extends('layouts.catalog')

@section('title', __('Вход'))

@section('content')
    <div class="mx-auto mt-6 max-w-md rounded-xl border border-line bg-surface p-7">
        <h1 class="mb-6 text-2xl font-semibold">{{ __('Вход') }}</h1>
        <x-breeze.auth-session-status class="mb-4" :status="session('status')" />
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf
            <div>
                <label for="email" class="mb-2 block text-sm">{{ __('Email') }}</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="table-filter w-full">
                <x-breeze.input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
            <div>
                <label for="password" class="mb-2 block text-sm">{{ __('Пароль') }}</label>
                <input id="password" name="password" type="password" required autocomplete="current-password" class="table-filter w-full">
                <x-breeze.input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <div class="flex flex-wrap items-center justify-between gap-3 text-sm">
                <label class="flex items-center gap-2"><input type="checkbox" name="remember" class="rounded border-line text-accent">{{ __('Запомнить меня') }}</label>
                <a href="{{ route('password.request') }}" class="text-link underline underline-offset-4">{{ __('Забыли пароль?') }}</a>
            </div>
            <button class="w-full primary-action">{{ __('Войти') }}</button>
        </form>
        <p class="mt-6 text-sm text-muted">{{ __('Нет аккаунта?') }} <a href="{{ route('register') }}" class="text-link underline underline-offset-4">{{ __('Создать аккаунт') }}</a></p>
    </div>
@endsection
