<form method="GET" action="{{ url()->current() }}" class="mb-4 flex flex-wrap items-end gap-3 text-sm">
    <input type="hidden" name="sort" value="{{ $filters['sort'] ?? 'title' }}">
    <input type="hidden" name="direction" value="{{ $filters['direction'] ?? 'asc' }}">
    <div class="min-w-48 flex-1">
        <label for="q" class="mb-1.5 block text-xs text-[#687362]">{{ __('Поиск') }}</label>
        <input id="q" name="q" type="search" maxlength="120" value="{{ $filters['q'] ?? '' }}" placeholder="{{ __('Название, автор, страна или год') }}" class="table-filter w-full">
    </div>
    <div class="w-48 grow sm:grow-0">
        <label for="author" class="mb-1.5 block text-xs text-[#687362]">{{ __('Автор') }}</label>
        <select id="author" name="author" class="table-filter w-full">
            <option value="">{{ __('Все авторы') }}</option>
            @foreach ($authors as $authorOption)
                <option value="{{ $authorOption->id }}" @selected(($filters['author'] ?? '') == $authorOption->id)>{{ $authorOption->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="w-40 grow sm:grow-0">
        <label for="country" class="mb-1.5 block text-xs text-[#687362]">{{ __('Страна') }}</label>
        <select id="country" name="country" class="table-filter w-full">
            <option value="">{{ __('Все страны') }}</option>
            @foreach ($authors->whereNotNull('country')->unique('country')->sortBy('country') as $countryAuthor)
                <option value="{{ $countryAuthor->country }}" @selected(($filters['country'] ?? '') === $countryAuthor->country)>{{ $countryAuthor->flag }} {{ __($countryAuthor->country) }}</option>
            @endforeach
        </select>
    </div>
    <div class="w-48 grow sm:grow-0">
        <label for="genre" class="mb-1.5 block text-xs text-[#687362]">{{ __('Жанр') }}</label>
        <select id="genre" name="genre" class="table-filter w-full">
            <option value="">{{ __('Все жанры') }}</option>
            @foreach ($genres as $genre)
                <option value="{{ $genre }}" @selected(($filters['genre'] ?? '') === $genre)>{{ __($genre) }}</option>
            @endforeach
        </select>
    </div>
    <button class="rounded bg-[#36583c] px-4 py-2 text-white hover:bg-[#29452e]">{{ __('Применить') }}</button>
    @if (collect($filters)->except(['sort', 'direction'])->filter()->isNotEmpty())
        <a href="{{ url()->current() }}" class="px-1 py-2 text-[#687362] underline underline-offset-4">{{ __('Сбросить') }}</a>
    @endif
</form>
@if ($errors->any())
    <p role="alert" class="mb-4 text-sm text-red-800">{{ $errors->first() }}</p>
@endif
