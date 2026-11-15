@extends('layouts.catalog')

@section('title', 'Регистрация')

@section('content')
    <div class="mx-auto mt-6 max-w-md rounded-md border border-[#cfd7c8] bg-white p-7">
        <h1 class="mb-6 text-2xl font-semibold">Регистрация</h1>
        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf
            <div>
                <label for="name" class="mb-2 block text-sm">Имя</label>
                <input id="name" name="name" value="{{ old('name') }}" maxlength="255" required autofocus autocomplete="name" class="table-filter w-full">
                <x-breeze.input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            <div>
                <label for="email" class="mb-2 block text-sm">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username" class="table-filter w-full">
                <x-breeze.input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
            <div>
                <label for="password" class="mb-2 block text-sm">Пароль</label>
                <input id="password" name="password" type="password" minlength="8" required autocomplete="new-password" aria-describedby="password-hint" class="table-filter w-full">
                <p id="password-hint" class="mt-1 text-xs text-[#687362]">Не менее 8 символов.</p>
                <x-breeze.input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <div>
                <label for="password_confirmation" class="mb-2 block text-sm">Повторите пароль</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="table-filter w-full">
            </div>
            <button class="w-full rounded bg-[#36583c] px-5 py-2.5 text-sm text-white hover:bg-[#29452e]">Создать аккаунт</button>
        </form>
        <p class="mt-6 text-sm text-[#687362]">Уже есть аккаунт? <a href="{{ route('login') }}" class="text-[#45623d] underline underline-offset-4">Войти</a></p>
    </div>
@endsection
