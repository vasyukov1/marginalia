@extends('layouts.catalog')

@section('content')
    <h1 class="mb-5 text-2xl font-semibold">Книги</h1>
    @include('books.filters')
    @include('books.table', ['personal' => false])
@endsection
