@extends('layouts.catalog')

@section('title', __('Редактировать книгу'))

@section('content')
    <div class="mx-auto max-w-3xl">
        <a href="{{ route('books.show', $book) }}" class="text-sm text-muted underline underline-offset-4">{{ __('Назад к книге') }}</a>
        <h1 class="mt-5 mb-6 text-2xl font-semibold">{{ __('Редактировать книгу') }}</h1>
        <form method="POST" action="{{ route('books.update', $book) }}" class="space-y-5 rounded-xl border border-line bg-surface p-5 sm:p-7">
            @csrf
            @method('PATCH')
            <p class="text-sm text-muted">{{ __('Изменения будут видны в общем каталоге. Ваши оценки и заметки сохранятся.') }}</p>
            @if ($errors->any())
                <ul role="alert" class="space-y-1 text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
            <div>
                <label for="title" class="mb-2 block text-sm">{{ __('Название на русском *') }}</label>
                <input id="title" name="title" value="{{ old('title', $book->title) }}" required maxlength="255" class="table-filter w-full">
            </div>
            <div>
                <label for="title_en" class="mb-2 block text-sm">{{ __('Название на английском') }}</label>
                <input id="title_en" name="title_en" value="{{ old('title_en', $book->title_en) }}" maxlength="255" class="table-filter w-full">
            </div>
            <div>
                <label for="author_id" class="mb-2 block text-sm">{{ __('Автор *') }}</label>
                <select id="author_id" name="author_id" required class="table-filter w-full">
                    @foreach ($authors as $author)
                        <option value="{{ $author->id }}" @selected(old('author_id', $book->author_id) == $author->id)>{{ $author->localized_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="publication_year" class="mb-2 block text-sm">{{ __('Год издания') }}</label>
                    <input id="publication_year" name="publication_year" type="number" min="1" max="{{ now()->year + 1 }}" value="{{ old('publication_year', $book->publication_year) }}" class="table-filter w-full">
                </div>
                <div>
                    <label for="pages" class="mb-2 block text-sm">{{ __('Страницы') }}</label>
                    <input id="pages" name="pages" type="number" min="1" max="100000" value="{{ old('pages', $book->pages) }}" class="table-filter w-full">
                </div>
            </div>
            <div>
                <label for="genre" class="mb-2 block text-sm">{{ __('Жанр') }}</label>
                <input id="genre" name="genre" maxlength="100" value="{{ old('genre', $book->genre) }}" class="table-filter w-full">
            </div>
            <div>
                <label for="description" class="mb-2 block text-sm">{{ __('Описание') }}</label>
                <textarea id="description" name="description" rows="5" maxlength="10000" class="table-filter w-full">{{ old('description', $book->description) }}</textarea>
            </div>
            <div class="flex flex-wrap items-center gap-4">
                <button class="primary-action">{{ __('Сохранить') }}</button>
                <a href="{{ route('books.show', $book) }}" class="secondary-action">{{ __('Отмена') }}</a>
            </div>
        </form>
    </div>
@endsection
