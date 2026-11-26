@extends('layouts.catalog')

@section('title', __('Добавить прочитанную книгу'))

@section('content')
    <div class="mx-auto max-w-3xl">
        <h1 class="mb-6 text-2xl font-semibold">{{ __('Добавить прочитанную книгу') }}</h1>
        <form method="POST" action="{{ route('readings.store') }}" class="space-y-6 rounded-xl border border-line bg-surface p-5 sm:p-7"
              x-data="{ mode: @js(old('mode', 'existing')) }">
            @csrf
            @if ($errors->any())
                <ul class="space-y-1 text-sm text-red-700" role="alert">
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
            <fieldset x-show="mode === 'existing'" :disabled="mode !== 'existing'"
                      x-data="readingPicker(@js($bookSuggestions), @js($authorSuggestions), @js(old('book_id')), @js(old('catalog_title', '')), @js(old('catalog_author', '')))"
                      class="space-y-4">
                <legend class="sr-only">{{ __('Выберите книгу из каталога.') }}</legend>
                <input type="hidden" name="book_id" :value="bookId">
                @foreach (['title' => __('Название'), 'author' => __('Автор')] as $field => $label)
                    <div class="relative" @click.outside="if (activeField === '{{ $field }}') close()" @focusout="if (!$el.contains($event.relatedTarget)) close()">
                        <label for="catalog_{{ $field }}" class="mb-2 block text-sm">{{ $label }}</label>
                        <input id="catalog_{{ $field }}" name="catalog_{{ $field }}" type="text" maxlength="255"
                               :value="{{ $field }}" @input="edit('{{ $field }}', $event.target.value)" @focus="open('{{ $field }}')"
                               @keydown.arrow-down.prevent="move('{{ $field }}', 1)" @keydown.arrow-up.prevent="move('{{ $field }}', -1)"
                               @keydown.enter="if (activeField === '{{ $field }}') { $event.preventDefault(); chooseActive('{{ $field }}'); }"
                               @keydown.escape.prevent="close()" autocomplete="off" role="combobox" aria-autocomplete="list"
                               aria-controls="{{ $field }}-suggestions" :aria-expanded="activeField === '{{ $field }}'"
                               :aria-activedescendant="activeField === '{{ $field }}' && activeIndex >= 0 ? '{{ $field }}-option-' + activeIndex : null"
                               placeholder="{{ __('Начните вводить…') }}" class="table-filter w-full">
                        <ul id="{{ $field }}-suggestions" role="listbox" aria-label="{{ $label }}" x-cloak
                            x-show="activeField === '{{ $field }}'"
                            class="absolute z-40 mt-1 max-h-64 w-full overflow-y-auto rounded-xl border border-line bg-surface shadow-xl">
                            <template x-for="(item, index) in suggestions('{{ $field }}')" :key="item.id">
                                <li role="option" :id="'{{ $field }}-option-' + index" :aria-selected="activeIndex === index">
                                    <button type="button" tabindex="-1" @mousedown.prevent @click="choose('{{ $field }}', item)"
                                            :class="activeIndex === index ? 'bg-surface-hover' : ''"
                                            class="block w-full px-4 py-3 text-left text-sm hover:bg-surface-hover">
                                        <span class="block" x-text="item.label"></span>
                                        @if ($field === 'title')
                                            <span class="mt-1 block text-xs text-muted" x-text="item.author"></span>
                                        @endif
                                    </button>
                                </li>
                            </template>
                            <li x-show="suggestions('{{ $field }}').length === 0" role="presentation" class="px-4 py-3 text-sm text-muted">
                                {{ __('Совпадений нет. Можно добавить новую книгу.') }}
                            </li>
                        </ul>
                    </div>
                @endforeach
                <p class="text-sm text-muted" role="status" x-show="bookId">{{ __('Книга выбрана. Автор заполнен автоматически.') }}</p>
            </fieldset>
            <fieldset x-show="mode === 'new'" :disabled="mode !== 'new'" class="space-y-4">
                <legend class="sr-only">{{ __('Новая книга') }}</legend>
                <p class="text-sm text-muted">{{ __('Книга появится в общем каталоге. Если автор и название уже есть, используем существующую запись.') }}</p>
                <div>
                    <label for="title" class="mb-2 block text-sm">{{ __('Название на русском *') }}</label>
                    <input id="title" name="title" value="{{ old('title') }}" maxlength="255" :required="mode === 'new'" class="table-filter w-full">
                </div>
                <div>
                    <label for="author_name" class="mb-2 block text-sm">{{ __('Автор на русском *') }}</label>
                    <input id="author_name" name="author_name" list="authors" value="{{ old('author_name') }}" maxlength="255" :required="mode === 'new'" class="table-filter w-full">
                    <datalist id="authors">
                        @foreach ($authors as $author)
                            <option value="{{ $author->name }}"></option>
                        @endforeach
                    </datalist>
                </div>
                <details @if (old('title_en') || old('author_name_en')) open @endif class="rounded-xl border border-line p-4">
                    <summary class="cursor-pointer text-sm">{{ __('Английский перевод (необязательно)') }}</summary>
                    <p class="mt-3 text-xs text-muted">{{ __('Без перевода показывается исходное название. Перевод автора сохраняется только для нового автора.') }}</p>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="title_en" class="mb-2 block text-sm">{{ __('Название на английском') }}</label>
                            <input id="title_en" name="title_en" value="{{ old('title_en') }}" maxlength="255" class="table-filter w-full">
                        </div>
                        <div>
                            <label for="author_name_en" class="mb-2 block text-sm">{{ __('Автор на английском') }}</label>
                            <input id="author_name_en" name="author_name_en" value="{{ old('author_name_en') }}" maxlength="255" class="table-filter w-full">
                        </div>
                    </div>
                </details>
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
