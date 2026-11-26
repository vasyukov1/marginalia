@extends('layouts.catalog')

@section('content')
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-semibold">{{ __('Библиотека') }}</h1>
        <a href="{{ route('readings.create') }}" class="primary-action">{{ __('Добавить прочитанную книгу') }}</a>
    </div>
    @include('books.filters')
    @include('books.table', ['personal' => false])
@endsection
