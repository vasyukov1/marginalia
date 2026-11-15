<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Каталог') · Marginalia</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f7f5ef] font-sans text-[#292d25] antialiased selection:bg-[#dce3cf]">
    <a href="#content" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:bg-white focus:p-3">К содержимому</a>
    <div class="mx-auto flex min-h-screen max-w-6xl flex-col px-5 sm:px-10">
        <header class="flex flex-wrap items-center justify-between gap-5 border-b border-[#d9d9ce] py-7">
            <a href="{{ route('welcome') }}" class="flex items-baseline gap-2" aria-label="Marginalia — каталог">
                <span class="font-serif text-3xl tracking-tight">marginalia<span class="text-[#738064]">.</span></span>
            </a>
            <nav aria-label="Основная навигация" class="flex items-center gap-6 text-sm">
                <a href="{{ route('welcome') }}" class="underline decoration-[#8c977b] underline-offset-8">Каталог</a>
                @auth
                    <a href="{{ route('profile.edit') }}" class="hover:text-[#66764f]">{{ auth()->user()->name }}</a>
                @else
                    <a href="{{ route('login') }}" class="hover:text-[#66764f]">Войти <span aria-hidden="true">↗</span></a>
                @endauth
            </nav>
        </header>
        <main id="content" class="flex-1 py-12 sm:py-16">
            @yield('content')
        </main>
        <footer class="flex flex-wrap justify-between gap-3 border-t border-[#d9d9ce] py-6 text-xs text-[#6d7165]">
            <span>Marginalia — заметки на полях</span>
            <span>Личная история чтения</span>
        </footer>
    </div>
</body>
</html>
