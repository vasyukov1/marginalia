@extends('layouts.catalog')

@section('title', $author->localized_name)

@section('content')
    <div class="mb-5 flex flex-wrap items-center gap-4">
        <x-author-avatar :author="$author" class="size-20" />
        <h1 class="text-2xl font-semibold">{{ $author->localized_name }}</h1>
        <span class="text-sm text-muted"><span aria-hidden="true">{{ $author->flag }}</span> {{ __($author->country ?? '—') }}</span>
        @can('update-author', $author)
            <a href="{{ route('authors.edit', $author) }}" class="secondary-action sm:ml-auto">{{ __('Редактировать автора') }}</a>
        @endcan
    </div>
    @if ($author->avatar_source_url && $author->avatar_credit)
        <p class="mb-5 text-xs text-muted">
            {{ __('Фото:') }} <a href="{{ $author->avatar_source_url }}" target="_blank" rel="noopener noreferrer" class="underline underline-offset-4">{{ $author->avatar_credit }}</a>
        </p>
    @endif
    @if ($stats !== null)
        <h2 class="mb-4 text-sm font-medium">{{ __('Вы прочитали') }}</h2>
        @include('readings.stats', ['authorStats' => true])
    @endif
    @include('books.filters')
    @include('books.table', ['personal' => auth()->check()])
@endsection
