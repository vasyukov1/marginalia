@extends('layouts.catalog')

@section('title', __('Мои книги'))

@section('content')
    <div class="mb-5 flex flex-wrap items-baseline justify-between gap-3">
        <h1 class="text-2xl font-semibold">{{ $user->name }}</h1>
        <a href="{{ route('readings.create') }}" class="primary-action">{{ __('Добавить прочитанную книгу') }}</a>
    </div>
    @include('readings.stats')
    <details class="mb-7 rounded-xl border border-line p-4">
        <summary class="cursor-pointer font-semibold text-brand">{{ __('Статистика по годам и сезонам') }}</summary>
        <p class="my-4 text-sm text-muted">{{ __('За всё время. Зима 2017–18 учитывается в 2018 году. Нераспознанные периоды показаны отдельно.') }}</p>
        @include('readings.summary-table', ['heading' => __('По годам'), 'column' => __('Год чтения'), 'rows' => $yearStats])
        @include('readings.summary-table', ['heading' => __('По сезонам каждого года'), 'column' => __('Период'), 'rows' => $seasonStats])
    </details>
    <h2 class="mb-4 text-lg font-semibold">{{ __('Прочитанные книги') }}</h2>
    @if ($stats['books'] === 0)
        <p class="mb-5 text-sm text-muted">{{ __('Добавьте первую прочитанную книгу.') }}</p>
    @endif
    @include('books.filters')
    @include('books.table', ['personal' => true])
    <div class="mt-7">
        <p class="mb-3 text-sm text-muted">{{ __('Страны авторов за выбранный год и сезон. Остальные фильтры влияют только на список книг.') }}</p>
        @include('readings.summary-table', ['heading' => __('По странам'), 'column' => __('Страна'), 'rows' => $countryStats])
    </div>
@endsection
