<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <!-- Tailwind CSS & Alpine.js via CDN -->
            <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
            <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.1/dist/cdn.min.js"></script>
        @endif
    </head>
    <body class="font-sans text-ink antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-canvas">
            <div>
                <a href="/">
                    <span class="font-serif text-3xl tracking-tight text-ink">marginalia.</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-7 border border-line bg-surface shadow-md overflow-hidden sm:rounded-xl">
                <div class="mb-5"><x-language-switcher /></div>
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
