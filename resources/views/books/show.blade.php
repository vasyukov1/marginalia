@extends('layouts.catalog')

@section('title', $book->title)

@section('content')
    <div class="mx-auto max-w-4xl">
        <a href="{{ route('welcome') }}" class="text-sm text-[#687362] underline underline-offset-4">Все книги</a>
        <h1 class="mt-6 text-3xl leading-tight font-semibold">{{ $book->title }}</h1>
        <a href="{{ route('authors.show', $book->author) }}" class="mt-3 inline-block text-lg text-[#45623d] underline underline-offset-4">{{ $book->author->name }}</a>
        <dl class="my-7 grid grid-cols-2 gap-6 border-y border-[#dce1d7] py-5 text-sm sm:grid-cols-4">
            <div><dt class="text-xs text-[#687362]">Страна</dt><dd class="mt-2">{{ $book->author->flag }} {{ $book->author->country ?? '—' }}</dd></div>
            <div><dt class="text-xs text-[#687362]">Год</dt><dd class="mt-2">{{ $book->publication_year ?? '—' }}</dd></div>
            <div><dt class="text-xs text-[#687362]">Жанр</dt><dd class="mt-2">{{ $book->genre ?? '—' }}</dd></div>
            <div><dt class="text-xs text-[#687362]">Страницы</dt><dd class="mt-2">{{ $book->pages ?? '—' }}</dd></div>
        </dl>
        @if ($book->description)
            <p class="mb-7 whitespace-pre-line leading-relaxed">{{ $book->description }}</p>
        @endif
        @if ($book->isbn)
            <p class="mb-7 text-sm text-[#687362]">ISBN: {{ $book->isbn }}</p>
        @endif
        @auth
            <section class="rounded-md border border-[#cfd7c8] bg-white p-5 sm:p-7">
                <h2 class="mb-5 text-xl font-semibold">{{ $reading ? 'Ваша запись о чтении' : 'Отметить прочитанной' }}</h2>
                <form method="POST" action="{{ route('readings.update', $book) }}" class="space-y-5">
                    @csrf
                    @method('PUT')
                    <div class="flex flex-wrap gap-5">
                        <div class="min-w-48 flex-1">
                            <label for="period" class="mb-2 block text-sm">Когда прочитано</label>
                            <input id="period" name="period" maxlength="100" value="{{ old('period', $reading?->period) }}" placeholder="Например, лето 2020" class="table-filter w-full">
                            <x-breeze.input-error :messages="$errors->get('period')" class="mt-2" />
                        </div>
                        <div class="w-40">
                            <label for="rating" class="mb-2 block text-sm">Оценка / 10</label>
                            <select id="rating" name="rating" class="table-filter w-full">
                                <option value="">Без оценки</option>
                                @for ($score = 1; $score <= 10; $score++)
                                    <option value="{{ $score }}" @selected(old('rating', $reading?->rating) == $score)>{{ $score }}</option>
                                @endfor
                            </select>
                            <x-breeze.input-error :messages="$errors->get('rating')" class="mt-2" />
                        </div>
                    </div>
                    <div>
                        <label for="notes" class="mb-2 block text-sm">Личная заметка</label>
                        <textarea id="notes" name="notes" maxlength="2000" rows="3" class="table-filter w-full">{{ old('notes', $reading?->notes) }}</textarea>
                        <x-breeze.input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>
                    <button class="rounded bg-[#36583c] px-5 py-2.5 text-sm text-white hover:bg-[#29452e]">{{ $reading ? 'Сохранить' : 'Добавить в прочитанные' }}</button>
                </form>
                @if ($reading)
                    <form method="POST" action="{{ route('readings.destroy', $book) }}" class="mt-5">
                        @csrf
                        @method('DELETE')
                        <button class="text-sm text-[#8a5045] underline underline-offset-4">Убрать из прочитанных</button>
                    </form>
                @endif
            </section>
        @else
            <p class="text-sm"><a href="{{ route('login') }}" class="text-[#45623d] underline">Войдите</a> или <a href="{{ route('register') }}" class="text-[#45623d] underline">зарегистрируйтесь</a>, чтобы отметить книгу прочитанной.</p>
        @endauth
    </div>
@endsection
