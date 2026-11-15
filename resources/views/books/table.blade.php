@php
    $columns = ['title' => 'Название', 'author' => 'Автор', 'country' => 'Страна', 'year' => 'Год', 'genre' => 'Жанр', 'pages' => 'Страницы'];
    if ($personal) {
        $columns += ['period' => 'Прочитано', 'rating' => 'Оценка / 10'];
    }
@endphp
<div role="region" aria-label="Таблица книг" tabindex="0" class="book-sheet overflow-auto rounded-md border border-[#cfd7c8] bg-white focus-visible:outline-2 focus-visible:outline-[#66764f]">
    <table class="w-full border-separate border-spacing-0 text-left text-sm">
        <caption class="sr-only">Книги. Нажмите на заголовок столбца для сортировки.</caption>
        <thead>
            <tr>
                <th scope="col" class="sheet-number sticky top-0 left-0 z-30 bg-[#e9eee4] px-3 py-3 text-right text-xs font-medium text-[#687362]">№</th>
                @foreach ($columns as $key => $label)
                    @php
                        $active = ($filters['sort'] ?? 'title') === $key;
                        $direction = $active && ($filters['direction'] ?? 'asc') === 'asc' ? 'desc' : 'asc';
                    @endphp
                    <th scope="col" aria-sort="{{ $active ? (($filters['direction'] ?? 'asc') === 'asc' ? 'ascending' : 'descending') : 'none' }}" class="sticky top-0 z-20 border-b border-l border-[#cfd7c8] bg-[#e9eee4] text-xs font-semibold text-[#465840]">
                        <a href="{{ url()->current().'?'.http_build_query(array_merge($filters, ['sort' => $key, 'direction' => $direction])) }}" class="flex items-center justify-between gap-4 whitespace-nowrap px-4 py-3 hover:bg-[#dce7d4]" aria-label="{{ $label }}: {{ $direction === 'asc' ? 'по возрастанию' : 'по убыванию' }}">
                            {{ $label }} <span aria-hidden="true" class="{{ $active ? 'text-[#36583c]' : 'text-[#899582]' }}">{{ $active ? (($filters['direction'] ?? 'asc') === 'asc' ? '↑' : '↓') : '↕' }}</span>
                        </a>
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($books as $book)
                <tr class="group even:bg-[#f8faf6] hover:bg-[#edf4e7]">
                    <td class="sheet-number sticky left-0 z-10 border-b border-[#e1e6dc] bg-[#f0f3ec] px-3 py-3 text-right text-xs tabular-nums text-[#73806b] group-hover:bg-[#e1ecd9]">{{ $loop->iteration }}</td>
                    <th scope="row" class="min-w-72 max-w-md border-b border-l border-[#e1e6dc] px-4 py-3 font-medium">
                        <a href="{{ route('books.show', $book) }}" class="hover:text-[#36583c] hover:underline underline-offset-4">{{ $book->title }}</a>
                    </th>
                    <td class="min-w-44 border-b border-l border-[#e1e6dc] px-4 py-3"><a href="{{ route('authors.show', $book->author) }}" class="text-[#45623d] underline-offset-4 hover:underline">{{ $book->author->name }}</a></td>
                    <td class="whitespace-nowrap border-b border-l border-[#e1e6dc] px-4 py-3"><span aria-hidden="true" class="mr-1.5">{{ $book->author->flag }}</span>{{ $book->author->country ?? '—' }}</td>
                    <td class="border-b border-l border-[#e1e6dc] px-4 py-3 text-right tabular-nums">{{ $book->publication_year ?? '—' }}</td>
                    <td class="min-w-40 border-b border-l border-[#e1e6dc] px-4 py-3 text-[#687362]">{{ $book->genre ?? '—' }}</td>
                    <td class="border-b border-l border-[#e1e6dc] px-4 py-3 text-right tabular-nums">{{ $book->pages ?? '—' }}</td>
                    @if ($personal)
                        @php($reading = $book->readings->first())
                        <td class="min-w-36 border-b border-l border-[#e1e6dc] px-4 py-3">{{ $reading?->period ?? '—' }}</td>
                        <td class="border-b border-l border-[#e1e6dc] px-4 py-3 text-right tabular-nums">
                            @if ($reading?->rating !== null)
                                <span class="rounded bg-[#e7efdf] px-2 py-1 font-medium text-[#3b5e30]">{{ $reading->rating }}</span>
                            @else
                                <span class="text-[#899582]">—</span>
                            @endif
                        </td>
                    @endif
                </tr>
            @empty
                <tr><td colspan="{{ count($columns) + 1 }}" class="px-6 py-14 text-center text-[#687362]">Книги не найдены</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
