<form method="GET" action="{{ url()->current() }}" class="mb-4 flex flex-wrap items-end gap-3 text-sm">
    <input type="hidden" name="sort" value="{{ $filters['sort'] ?? 'title' }}">
    <input type="hidden" name="direction" value="{{ $filters['direction'] ?? 'asc' }}">
    <div class="min-w-48 flex-1">
        <label for="q" class="mb-1.5 block text-xs text-[#687362]">Поиск</label>
        <input id="q" name="q" type="search" maxlength="120" value="{{ $filters['q'] ?? '' }}" placeholder="Название или автор" class="table-filter w-full">
    </div>
    <div class="w-48 grow sm:grow-0">
        <label for="author" class="mb-1.5 block text-xs text-[#687362]">Автор</label>
        <select id="author" name="author" class="table-filter w-full">
            <option value="">Все авторы</option>
            @foreach ($authors as $authorOption)
                <option value="{{ $authorOption->id }}" @selected(($filters['author'] ?? '') == $authorOption->id)>{{ $authorOption->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="w-40 grow sm:grow-0">
        <label for="country" class="mb-1.5 block text-xs text-[#687362]">Страна</label>
        <select id="country" name="country" class="table-filter w-full">
            <option value="">Все страны</option>
            @foreach ($authors->whereNotNull('country')->unique('country')->sortBy('country') as $countryAuthor)
                <option value="{{ $countryAuthor->country }}" @selected(($filters['country'] ?? '') === $countryAuthor->country)>{{ $countryAuthor->flag }} {{ $countryAuthor->country }}</option>
            @endforeach
        </select>
    </div>
    <div class="w-48 grow sm:grow-0">
        <label for="genre" class="mb-1.5 block text-xs text-[#687362]">Жанр</label>
        <select id="genre" name="genre" class="table-filter w-full">
            <option value="">Все жанры</option>
            @foreach ($genres as $genre)
                <option value="{{ $genre }}" @selected(($filters['genre'] ?? '') === $genre)>{{ $genre }}</option>
            @endforeach
        </select>
    </div>
    <button class="rounded bg-[#36583c] px-4 py-2 text-white hover:bg-[#29452e]">Применить</button>
    @if (collect($filters)->except(['sort', 'direction'])->filter()->isNotEmpty())
        <a href="{{ url()->current() }}" class="px-1 py-2 text-[#687362] underline underline-offset-4">Сбросить</a>
    @endif
</form>
@if ($errors->any())
    <p role="alert" class="mb-4 text-sm text-red-800">{{ $errors->first() }}</p>
@endif
