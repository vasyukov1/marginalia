@extends('layouts.catalog')

@section('title', __('Вход'))

@section('content')
    <div class="mx-auto mt-6 max-w-md rounded-md border border-[#cfd7c8] bg-white p-7">
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
                <label class="flex items-center gap-2"><input type="checkbox" name="remember" class="rounded border-[#cfd7c8] text-[#36583c]">{{ __('Запомнить меня') }}</label>
                <a href="{{ route('password.request') }}" class="text-[#45623d] underline underline-offset-4">{{ __('Забыли пароль?') }}</a>
            </div>
            <button class="w-full rounded bg-[#36583c] px-5 py-2.5 text-sm text-white hover:bg-[#29452e]">{{ __('Войти') }}</button>
        </form>
        <p class="mt-6 text-sm text-[#687362]">{{ __('Нет аккаунта?') }} <a href="{{ route('register') }}" class="text-[#45623d] underline underline-offset-4">{{ __('Создать аккаунт') }}</a></p>
    </div>
@endsection
