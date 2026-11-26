@php
    $columns = ['title' => __('Название'), 'author' => __('Автор'), 'country' => __('Страна'), 'year' => __('Год'), 'genre' => __('Жанр'), 'pages' => __('Страницы')];
    if ($personal) {
        $columns += ['period' => __('Прочитано'), 'rating' => __('Оценка / 10')];
    }
@endphp
<div role="region" aria-label="{{ __('Таблица книг') }}" tabindex="0" class="book-sheet overflow-auto rounded-xl border border-line bg-surface focus-visible:outline-2 focus-visible:outline-accent">
    <table class="w-full border-separate border-spacing-0 text-left text-sm">
        <caption class="sr-only">{{ __('Книги. Нажмите на заголовок столбца для сортировки.') }}</caption>
        <thead>
            <tr>
                <th scope="col" class="sheet-number sticky top-0 left-0 z-30 bg-brand px-3 py-3 text-right text-xs font-medium text-canvas">№</th>
                @foreach ($columns as $key => $label)
                    @php
                        $active = ($filters['sort'] ?? 'title') === $key;
                        $direction = $active && ($filters['direction'] ?? 'asc') === 'asc' ? 'desc' : 'asc';
                    @endphp
                    <th scope="col" aria-sort="{{ $active ? (($filters['direction'] ?? 'asc') === 'asc' ? 'ascending' : 'descending') : 'none' }}" class="sticky top-0 z-20 border-b border-l border-line bg-brand text-xs font-semibold text-canvas">
                        <a href="{{ url()->query(url()->current(), array_merge($filters, ['sort' => $key, 'direction' => $direction])) }}" class="flex items-center justify-between gap-4 whitespace-nowrap px-4 py-3 hover:bg-brand-hover" aria-label="{{ $label }}: {{ $direction === 'asc' ? __('по возрастанию') : __('по убыванию') }}">
                            {{ $label }} <span aria-hidden="true" class="{{ $active ? 'text-canvas' : 'text-canvas/60' }}">{{ $active ? (($filters['direction'] ?? 'asc') === 'asc' ? '↑' : '↓') : '↕︎' }}</span>
                        </a>
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($books as $book)
                <tr class="group even:bg-surface-raised hover:bg-surface-hover">
                    <td class="sheet-number sticky left-0 z-10 border-b border-line bg-surface-raised px-3 py-3 text-right text-xs tabular-nums text-muted group-hover:bg-surface-hover">{{ $loop->iteration }}</td>
                    <th scope="row" class="min-w-72 max-w-md border-b border-l border-line px-4 py-3 font-medium">
                        <a href="{{ route('books.show', $book) }}" class="hover:text-accent hover:underline underline-offset-4">{{ $book->localized_title }}</a>
                    </th>
                    <td class="min-w-44 border-b border-l border-line px-4 py-3"><a href="{{ route('authors.show', $book->author) }}" class="text-link underline-offset-4 hover:underline">{{ $book->author->localized_name }}</a></td>
                    <td class="whitespace-nowrap border-b border-l border-line px-4 py-3"><span aria-hidden="true" class="mr-1.5">{{ $book->author->flag }}</span>{{ __($book->author->country ?? '—') }}</td>
                    <td class="border-b border-l border-line px-4 py-3 text-right tabular-nums">{{ $book->publication_year ?? '—' }}</td>
                    <td class="min-w-40 border-b border-l border-line px-4 py-3 text-muted">{{ __($book->genre ?? '—') }}</td>
                    <td class="border-b border-l border-line px-4 py-3 text-right tabular-nums">{{ $book->pages ?? '—' }}</td>
                    @if ($personal)
                        @php($reading = $book->readings->first())
                        <td class="min-w-36 border-b border-l border-line px-4 py-3">{{ $reading?->period ?? '—' }}</td>
                        <td class="border-b border-l border-line px-4 py-3 text-right tabular-nums">
                            @if ($reading?->rating !== null)
                                <span class="rounded bg-surface-hover px-2 py-1 font-medium text-accent">{{ $reading->rating }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    @endif
                </tr>
            @empty
                <tr><td colspan="{{ count($columns) + 1 }}" class="px-6 py-14 text-center text-muted">{{ __('Книги не найдены') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
