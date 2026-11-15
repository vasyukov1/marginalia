@extends('layouts.catalog')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-6 pb-10">
        <div>
            <p class="mb-4 text-xs font-medium tracking-[0.2em] text-[#6d7165] uppercase">Книжная коллекция</p>
            <h1 class="font-serif text-5xl tracking-tight sm:text-6xl">На полке<span class="text-[#738064]">.</span></h1>
        </div>
        <p class="pb-1 text-sm text-[#6d7165]">Книг: {{ $bookCount }} <span class="px-2" aria-hidden="true">/</span> Авторов: {{ $authors->count() }}</p>
    </div>

    <form method="GET" action="{{ route('welcome') }}" class="mb-8 flex flex-col gap-4 border-y border-[#d9d9ce] py-5 sm:flex-row sm:items-end">
        <div class="min-w-0 flex-1">
            <label for="q" class="mb-2 block text-xs text-[#6d7165]">Название или автор</label>
            <input id="q" name="q" type="search" maxlength="120" value="{{ $filters['q'] ?? '' }}" placeholder="Найти книгу…" class="w-full rounded-sm border-[#cecec1] bg-transparent px-3 py-2.5 text-sm focus:border-[#66764f] focus:ring-[#66764f]">
        </div>
        <div class="sm:w-60">
            <label for="author" class="mb-2 block text-xs text-[#6d7165]">Автор</label>
            <select id="author" name="author" class="w-full rounded-sm border-[#cecec1] bg-transparent py-2.5 text-sm focus:border-[#66764f] focus:ring-[#66764f]">
                <option value="">Все авторы</option>
                @foreach ($authors as $author)
                    <option value="{{ $author->id }}" @selected(($filters['author'] ?? '') == $author->id)>{{ $author->name }}</option>
                @endforeach
            </select>
        </div>
        <button class="rounded-sm bg-[#3e503a] px-6 py-3 text-sm text-white transition hover:bg-[#2e3d2b] focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#3e503a]">Найти</button>
        @if (! empty($filters['q']) || ! empty($filters['author']))
            <a href="{{ route('welcome') }}" class="py-3 text-sm underline underline-offset-4">Сбросить</a>
        @endif
    </form>

    @if ($errors->any())
        <p role="alert" class="mb-5 text-sm text-red-800">{{ $errors->first() }}</p>
    @endif

    <div class="mb-3 flex justify-between text-xs text-[#6d7165]">
        <span>{{ ! empty($filters['q']) || ! empty($filters['author']) ? 'Найдено: '.$books->total() : 'Все книги' }}</span>
        <span>По названию</span>
    </div>

    <div role="region" aria-label="Таблица книг" tabindex="0" class="overflow-x-auto border-y border-[#d9d9ce] focus-visible:outline-2 focus-visible:outline-[#66764f]">
        <table class="w-full min-w-[760px] table-fixed text-left text-sm">
            <caption class="sr-only">Каталог книг: название, автор, жанр, год публикации и количество страниц</caption>
            <thead class="border-b border-[#d9d9ce] bg-[#eeeee5] text-xs text-[#626957]">
                <tr>
                    <th scope="col" class="w-[38%] px-4 py-4 font-medium">Название</th>
                    <th scope="col" class="w-[22%] px-4 py-4 font-medium">Автор</th>
                    <th scope="col" class="w-[22%] px-4 py-4 font-medium">Жанр</th>
                    <th scope="col" class="w-[8%] px-4 py-4 text-right font-medium">Год</th>
                    <th scope="col" class="w-[10%] px-4 py-4 text-right font-medium">Страницы</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#d9d9ce]">
                @forelse ($books as $book)
                    <tr class="align-top transition-colors hover:bg-[#eeeee5]/60">
                        <th scope="row" class="px-4 py-5 font-serif text-lg leading-snug font-normal">
                            <a href="{{ route('books.show', $book) }}" class="decoration-[#8c977b] underline-offset-4 hover:underline">{{ $book->title }}</a>
                        </th>
                        <td class="px-4 py-5 text-[#555c4d]">{{ $book->author->name }}</td>
                        <td class="px-4 py-5 text-[#6d7165]">{{ $book->genre ?? '—' }}</td>
                        <td class="px-4 py-5 text-right tabular-nums text-[#555c4d]">{{ $book->publication_year ?? '—' }}</td>
                        <td class="px-4 py-5 text-right tabular-nums text-[#555c4d]">{{ $book->pages ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-16 text-center">
                            <h2 class="font-serif text-3xl">Книги не найдены</h2>
                            <p class="mt-3 text-sm text-[#6d7165]">Попробуйте другое название или выберите всех авторов.</p>
                            <a href="{{ route('welcome') }}" class="mt-5 inline-block text-sm underline underline-offset-4">Вернуться к каталогу</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <p class="mt-3 text-xs text-[#6d7165] md:hidden">Прокрутите таблицу вправо, чтобы увидеть все столбцы.</p>

    @if ($books->hasPages())
        <nav aria-label="Страницы каталога" class="mt-7 flex items-center justify-between gap-4 text-sm">
            @if ($books->onFirstPage())
                <span class="text-[#6d7165]">← Назад</span>
            @else
                <a href="{{ $books->previousPageUrl() }}" rel="prev" class="underline underline-offset-4">← Назад</a>
            @endif
            <span class="text-[#6d7165]">{{ $books->currentPage() }} / {{ $books->lastPage() }}</span>
            @if ($books->hasMorePages())
                <a href="{{ $books->nextPageUrl() }}" rel="next" class="underline underline-offset-4">Далее →</a>
            @else
                <span class="text-[#6d7165]">Далее →</span>
            @endif
        </nav>
    @endif
@endsection
