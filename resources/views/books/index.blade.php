@extends('layouts.catalog')

@section('content')
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-semibold">{{ __('Книги') }}</h1>
        <a href="{{ route('readings.create') }}" class="rounded bg-[#36583c] px-5 py-2.5 text-sm text-white hover:bg-[#29452e]">{{ __('Добавить прочитанную книгу') }}</a>
    </div>
    @include('books.filters')
    @include('books.table', ['personal' => false])
@endsection
