@extends('layouts.catalog')

@section('title', __('Добавить прочитанную книгу'))

@section('content')
    <div class="mx-auto max-w-3xl">
        <h1 class="mb-6 text-2xl font-semibold">{{ __('Добавить прочитанную книгу') }}</h1>
        <form method="POST" action="{{ route('readings.store') }}" class="space-y-6 rounded-md border border-line bg-surface p-5 sm:p-7"
              x-data="{ mode: @js(old('mode', 'existing')) }">
            @csrf
            @if ($errors->any())
                <ul class="space-y-1 text-sm text-red-300" role="alert">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
            <fieldset>
                <legend class="sr-only">{{ __('Способ добавления') }}</legend>
                <div class="flex flex-wrap gap-5 border-b border-line pb-5 text-sm">
                    <label class="flex items-center gap-2"><input type="radio" name="mode" value="existing" x-model="mode" @checked(old('mode', 'existing') === 'existing')> {{ __('Из каталога') }}</label>
                    <label class="flex items-center gap-2"><input type="radio" name="mode" value="new" x-model="mode" @checked(old('mode') === 'new')> {{ __('Новой книги ещё нет') }}</label>
                </div>
            </fieldset>
            <div x-show="mode === 'existing'">
                <label for="book_id" class="mb-2 block text-sm">{{ __('Книга') }}</label>
                <select id="book_id" name="book_id" class="table-filter w-full" :disabled="mode !== 'existing'" :required="mode === 'existing'">
                    <option value="">{{ __('Выберите книгу') }}</option>
                    @foreach ($books as $book)
                        <option value="{{ $book->id }}" @selected(old('book_id') == $book->id)>{{ $book->title }} — {{ $book->author->name }}</option>
                    @endforeach
                </select>
            </div>
            <fieldset x-show="mode === 'new'" :disabled="mode !== 'new'" class="space-y-4">
                <legend class="sr-only">{{ __('Новая книга') }}</legend>
                <p class="text-sm text-muted">{{ __('Книга появится в общем каталоге. Если автор и название уже есть, используем существующую запись.') }}</p>
                <div>
                    <label for="title" class="mb-2 block text-sm">{{ __('Название *') }}</label>
                    <input id="title" name="title" value="{{ old('title') }}" maxlength="255" :required="mode === 'new'" class="table-filter w-full">
                </div>
                <div>
                    <label for="author_name" class="mb-2 block text-sm">{{ __('Автор *') }}</label>
                    <input id="author_name" name="author_name" list="authors" value="{{ old('author_name') }}" maxlength="255" :required="mode === 'new'" class="table-filter w-full">
                    <datalist id="authors">
                        @foreach ($authors as $author)
                            <option value="{{ $author->name }}"></option>
                        @endforeach
                    </datalist>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="country" class="mb-2 block text-sm">{{ __('Страна автора') }}</label>
                        <input id="country" name="country" list="countries" value="{{ old('country') }}" maxlength="100" class="table-filter w-full">
                        <datalist id="countries">
                            @foreach (['Англия', 'Великобритания', 'США', 'Франция', 'Италия', 'Ирландия', 'Россия', 'СССР', 'Беларусь', 'Украина', 'Германия', 'Япония', 'Испания', 'Канада'] as $country)
                                <option value="{{ $country }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                    <div>
                        <label for="genre" class="mb-2 block text-sm">{{ __('Жанр') }}</label>
                        <input id="genre" name="genre" value="{{ old('genre') }}" maxlength="100" class="table-filter w-full">
                    </div>
                    <div>
                        <label for="pages" class="mb-2 block text-sm">{{ __('Страницы') }}</label>
                        <input id="pages" name="pages" type="number" min="1" max="100000" value="{{ old('pages') }}" class="table-filter w-full">
                    </div>
                    <div>
                        <label for="publication_year" class="mb-2 block text-sm">{{ __('Год издания') }}</label>
                        <input id="publication_year" name="publication_year" type="number" min="1" max="{{ now()->year + 1 }}" value="{{ old('publication_year') }}" class="table-filter w-full">
                    </div>
                </div>
            </fieldset>
            <div class="grid gap-4 border-t border-line pt-5 sm:grid-cols-2">
                <div>
                    <label for="period" class="mb-2 block text-sm">{{ __('Когда прочитана') }}</label>
                    <input id="period" name="period" maxlength="100" value="{{ old('period') }}" placeholder="{{ __('Например, осень 2026') }}" class="table-filter w-full">
                </div>
                <div>
                    <label for="rating" class="mb-2 block text-sm">{{ __('Оценка / 10') }}</label>
                    <select id="rating" name="rating" class="table-filter w-full">
                        <option value="">{{ __('Без оценки') }}</option>
                        @for ($score = 1; $score <= 10; $score++)
                            <option value="{{ $score }}" @selected(old('rating') == $score)>{{ $score }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div>
                <label for="notes" class="mb-2 block text-sm">{{ __('Личная заметка') }}</label>
                <textarea id="notes" name="notes" rows="3" maxlength="2000" class="table-filter w-full">{{ old('notes') }}</textarea>
            </div>
            <div class="flex flex-wrap items-center gap-5">
                <button class="primary-action">{{ __('Добавить в прочитанные') }}</button>
                <a href="{{ route('books.index') }}" class="text-sm text-muted underline underline-offset-4">{{ __('Отмена') }}</a>
            </div>
        </form>
    </div>
@endsection
