@extends('layouts.catalog')

@section('title', $book->title)

@section('content')
    <div class="mx-auto max-w-4xl">
        <a href="{{ route('books.index') }}" class="text-sm text-muted underline underline-offset-4">{{ __('Все книги') }}</a>
        <h1 class="mt-6 text-3xl leading-tight font-semibold">{{ $book->title }}</h1>
        <a href="{{ route('authors.show', $book->author) }}" class="mt-3 inline-block text-lg text-link underline underline-offset-4">{{ $book->author->name }}</a>
        <dl class="my-7 grid grid-cols-2 gap-6 border-y border-line py-5 text-sm sm:grid-cols-4">
            <div><dt class="text-xs text-muted">{{ __('Страна') }}</dt><dd class="mt-2">{{ $book->author->flag }} {{ __($book->author->country ?? '—') }}</dd></div>
            <div><dt class="text-xs text-muted">{{ __('Год') }}</dt><dd class="mt-2">{{ $book->publication_year ?? '—' }}</dd></div>
            <div><dt class="text-xs text-muted">{{ __('Жанр') }}</dt><dd class="mt-2">{{ __($book->genre ?? '—') }}</dd></div>
            <div><dt class="text-xs text-muted">{{ __('Страницы') }}</dt><dd class="mt-2">{{ $book->pages ?? '—' }}</dd></div>
        </dl>
        @if ($book->description)
            <p class="mb-7 whitespace-pre-line leading-relaxed">{{ $book->description }}</p>
        @endif
        @if ($book->isbn)
            <p class="mb-7 text-sm text-muted">ISBN: {{ $book->isbn }}</p>
        @endif
        @auth
            <section class="rounded-md border border-line bg-surface p-5 sm:p-7">
                <h2 class="mb-5 text-xl font-semibold">{{ $reading ? __('Ваша запись о чтении') : __('Отметить прочитанной') }}</h2>
                <form method="POST" action="{{ route('readings.update', $book) }}" class="space-y-5">
                    @csrf
                    @method('PUT')
                    <div class="flex flex-wrap gap-5">
                        <div class="min-w-48 flex-1">
                            <label for="period" class="mb-2 block text-sm">{{ __('Когда прочитано') }}</label>
                            <input id="period" name="period" maxlength="100" value="{{ old('period', $reading?->period) }}" placeholder="{{ __('Например, лето 2020') }}" class="table-filter w-full">
                            <x-breeze.input-error :messages="$errors->get('period')" class="mt-2" />
                        </div>
                        <div class="w-40">
                            <label for="rating" class="mb-2 block text-sm">{{ __('Оценка / 10') }}</label>
                            <select id="rating" name="rating" class="table-filter w-full">
                                <option value="">{{ __('Без оценки') }}</option>
                                @for ($score = 1; $score <= 10; $score++)
                                    <option value="{{ $score }}" @selected(old('rating', $reading?->rating) == $score)>{{ $score }}</option>
                                @endfor
                            </select>
                            <x-breeze.input-error :messages="$errors->get('rating')" class="mt-2" />
                        </div>
                    </div>
                    <div>
                        <label for="notes" class="mb-2 block text-sm">{{ __('Личная заметка') }}</label>
                        <textarea id="notes" name="notes" maxlength="2000" rows="3" class="table-filter w-full">{{ old('notes', $reading?->notes) }}</textarea>
                        <x-breeze.input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>
                    <button class="primary-action">{{ $reading ? __('Сохранить') : __('Добавить в прочитанные') }}</button>
                </form>
                @if ($reading)
                    <form method="POST" action="{{ route('readings.destroy', $book) }}" class="mt-5">
                        @csrf
                        @method('DELETE')
                        <button class="text-sm text-red-300 underline underline-offset-4">{{ __('Убрать из прочитанных') }}</button>
                    </form>
                @endif
            </section>
        @else
            <p class="text-sm"><a href="{{ route('login') }}" class="text-link underline">{{ __('Войдите') }}</a> {{ __('или') }} <a href="{{ route('register') }}" class="text-link underline">{{ __('зарегистрируйтесь') }}</a>{{ __(', чтобы отметить книгу прочитанной.') }}</p>
        @endauth
    </div>
@endsection
