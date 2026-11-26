<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('Библиотека')) · Marginalia</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-canvas font-sans text-ink antialiased selection:bg-accent selection:text-canvas">
    <a href="#content" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:bg-surface focus:p-3">{{ __('К содержимому') }}</a>
    <header class="catalog-header flex flex-wrap items-center justify-between gap-4 border-b border-line bg-surface px-5 py-4 sm:px-8">
        <a href="{{ route('welcome') }}" class="font-serif text-2xl tracking-tight" aria-label="{{ __('Marginalia — библиотека') }}">marginalia.</a>
        <nav aria-label="{{ __('Основная навигация') }}" class="flex flex-wrap items-center gap-5 text-sm">
            <a href="{{ route('books.index') }}" @class(['font-semibold text-accent' => request()->routeIs('books.*')])>{{ __('Библиотека') }}</a>
            <a href="{{ route('authors.index') }}" @class(['font-semibold text-accent' => request()->routeIs('authors.*')])>{{ __('Авторы') }}</a>
            @auth
                <a href="{{ route('dashboard') }}" @class(['font-semibold text-accent' => request()->routeIs('dashboard')])>{{ __('Мои книги') }}</a>
                <a href="{{ route('profile.edit') }}">{{ __('Настройки') }}</a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="text-muted hover:underline">{{ __('Выйти') }}</button></form>
            @else
                <a href="{{ route('login') }}">{{ __('Войти') }}</a>
                <a href="{{ route('register') }}" class="rounded border border-line px-3 py-1.5 hover:bg-surface-raised">{{ __('Регистрация') }}</a>
            @endauth
            <x-language-switcher />
        </nav>
    </header>
    <main id="content" class="mx-auto max-w-[1600px] px-4 py-8 sm:px-8">
        @if (session('status'))
            <p role="status" class="mb-4 rounded border border-line bg-surface-raised px-4 py-3 text-sm text-ink">{{ session('status') }}</p>
        @endif
        @yield('content')
    </main>
</body>
</html>
