@extends('layouts.catalog')

@section('title', $author->name)

@section('content')
    <div class="mb-5 flex flex-wrap items-baseline gap-4">
        <h1 class="text-2xl font-semibold">{{ $author->name }}</h1>
        <span class="text-sm text-[#687362]"><span aria-hidden="true">{{ $author->flag }}</span> {{ $author->country }}</span>
    </div>
    @if ($stats !== null)
        <h2 class="mb-4 text-sm font-medium">Вы прочитали</h2>
        @include('readings.stats', ['authorStats' => true])
    @endif
    @include('books.filters')
    @include('books.table', ['personal' => auth()->check()])
@endsection
