@extends('layouts.catalog')

@section('title', __('Авторы'))

@section('content')
    <h1 class="mb-5 text-2xl font-semibold">{{ __('Авторы') }}</h1>
    <div role="region" aria-label="{{ __('Таблица авторов') }}" tabindex="0" class="overflow-auto rounded-xl border border-line bg-surface">
        <table class="w-full min-w-[600px] text-left text-sm">
            <thead class="bg-brand text-xs text-canvas">
                <tr><th scope="col" class="px-4 py-3">{{ __('Автор') }}</th><th scope="col" class="px-4 py-3">{{ __('Страна') }}</th><th scope="col" class="px-4 py-3 text-right">{{ __('Книг в библиотеке') }}</th>
                    @auth<th scope="col" class="px-4 py-3 text-right">{{ __('Вы прочитали') }}</th><th scope="col" class="px-4 py-3 text-right">{{ __('Ваша средняя оценка') }}</th>@endauth
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @forelse ($authors as $author)
                    <tr class="even:bg-surface-raised hover:bg-surface-hover">
                        <th scope="row" class="px-4 py-3 font-medium"><a href="{{ route('authors.show', $author) }}" class="flex items-center gap-3 text-link underline-offset-4 hover:underline"><x-author-avatar :author="$author" class="size-10" /><span>{{ $author->localized_name }}</span></a></th>
                        <td class="px-4 py-3"><span aria-hidden="true">{{ $author->flag }}</span> {{ __($author->country ?? '—') }}</td>
                        <td class="px-4 py-3 text-right tabular-nums">{{ $author->books_count }}</td>
                        @auth
                            <td class="px-4 py-3 text-right tabular-nums">{{ $author->readings_count }}</td>
                            <td class="px-4 py-3 text-right tabular-nums">{{ $author->readings_avg_rating !== null ? number_format($author->readings_avg_rating, 1, app()->getLocale() === 'ru' ? ',' : '.').' / 10' : '—' }}</td>
                        @endauth
                    </tr>
                @empty
                    <tr><td colspan="{{ auth()->check() ? 5 : 3 }}" class="px-4 py-12 text-center text-muted">{{ __('Авторов пока нет') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
