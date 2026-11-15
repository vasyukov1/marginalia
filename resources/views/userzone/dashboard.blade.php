@extends('layouts.catalog')

@section('title', __('Мои книги'))

@section('content')
    <div class="mb-5 flex flex-wrap items-baseline justify-between gap-3">
        <h1 class="text-2xl font-semibold">{{ $user->name }}</h1>
        <a href="{{ route('readings.create') }}" class="text-sm text-[#36583c] underline underline-offset-4">{{ __('Добавить прочитанную книгу') }}</a>
    </div>
    @include('readings.stats')
    <h2 class="mb-4 text-lg font-semibold">{{ __('Прочитанные книги') }}</h2>
    @if ($stats['books'] === 0)
        <p class="mb-5 text-sm text-[#687362]">{{ __('Добавьте первую прочитанную книгу.') }}</p>
    @endif
    @include('books.filters')
    @include('books.table', ['personal' => true])
@endsection
