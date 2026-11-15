<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Книги') · Marginalia</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f5f6f3] font-sans text-[#292d25] antialiased selection:bg-[#dce3cf]">
    <a href="#content" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:bg-white focus:p-3">К содержимому</a>
    <header class="flex flex-wrap items-center justify-between gap-4 border-b border-[#dce1d7] bg-white px-5 py-4 sm:px-8">
        <a href="{{ route('welcome') }}" class="font-serif text-2xl tracking-tight" aria-label="Marginalia — каталог">marginalia.</a>
        <nav aria-label="Основная навигация" class="flex flex-wrap items-center gap-5 text-sm">
            <a href="{{ route('welcome') }}" @class(['font-semibold text-[#3e603d]' => request()->routeIs('welcome')])>Книги</a>
            @auth
                <a href="{{ route('dashboard') }}" @class(['font-semibold text-[#3e603d]' => request()->routeIs('dashboard')])>Мои книги</a>
                <a href="{{ route('profile.edit') }}">Настройки</a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="text-[#687362] hover:underline">Выйти</button></form>
            @else
                <a href="{{ route('login') }}">Войти</a>
                <a href="{{ route('register') }}" class="rounded border border-[#cbd4c4] px-3 py-1.5 hover:bg-[#eef3e9]">Регистрация</a>
            @endauth
        </nav>
    </header>
    <main id="content" class="px-4 py-6 sm:px-8">
        @if (session('status'))
            <p role="status" class="mb-4 rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-900">{{ session('status') }}</p>
        @endif
        @yield('content')
    </main>
</body>
</html>
