@extends('layouts.catalog')

@section('title', $book->title)

@section('content')
    <a href="{{ route('welcome') }}" class="text-sm text-[#6d7165] underline underline-offset-4">← Все книги</a>
    <div class="mt-10 grid gap-10 md:grid-cols-[220px_1fr] md:gap-16">
        <div aria-hidden="true" class="flex aspect-[3/4] max-w-56 flex-col justify-between border-l-8 border-[#637652] bg-[#e2e7d9] p-7 text-[#4c6040]">
            <span class="text-xs tracking-widest uppercase">Marginalia</span>
            <span class="font-serif text-7xl">{{ mb_substr($book->author->name, 0, 1) }}.</span>
            <span class="text-sm">{{ $book->publication_year ?? 'Книжная коллекция' }}</span>
        </div>
        <div class="min-w-0">
            <p class="mb-4 text-xs tracking-widest text-[#6d7165] uppercase">{{ $book->genre ?? 'Книга' }}</p>
            <h1 class="font-serif text-4xl leading-tight tracking-tight sm:text-5xl">{{ $book->title }}</h1>
            <a href="{{ route('authors.show', $book->author) }}" class="mt-5 inline-block text-lg text-[#586b48] underline decoration-[#aab59a] underline-offset-4">{{ $book->author->name }}</a>
            <dl class="mt-10 grid grid-cols-2 gap-6 border-y border-[#d9d9ce] py-6 text-sm sm:grid-cols-3">
                <div><dt class="text-xs text-[#6d7165]">Год публикации</dt><dd class="mt-2">{{ $book->publication_year ?? 'Не указан' }}</dd></div>
                <div><dt class="text-xs text-[#6d7165]">Страницы</dt><dd class="mt-2">{{ $book->pages ?? 'Не указаны' }}</dd></div>
                <div><dt class="text-xs text-[#6d7165]">Страна автора</dt><dd class="mt-2">{{ $book->author->country ?? 'Не указана' }}</dd></div>
                @if ($book->isbn)
                    <div><dt class="text-xs text-[#6d7165]">ISBN</dt><dd class="mt-2">{{ $book->isbn }}</dd></div>
                @endif
            </dl>
            @if ($book->description)
                <p class="mt-8 whitespace-pre-line leading-relaxed text-[#555c4d]">{{ $book->description }}</p>
            @endif
        </div>
    </div>
@endsection
