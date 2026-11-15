@extends('layouts.catalog')

@section('content')
    <div class="mx-auto max-w-3xl py-8">
        <a href="{{ route('readings.create') }}" class="inline-block rounded bg-[#36583c] px-5 py-3 text-sm text-white hover:bg-[#29452e]">Добавить прочитанную книгу</a>
        <div class="mt-8 grid gap-4 sm:grid-cols-2">
            <a href="{{ route('books.index') }}" class="rounded-md border border-[#cfd7c8] bg-white p-6 text-xl font-semibold hover:bg-[#eef3e9]">Книги</a>
            <a href="{{ route('authors.index') }}" class="rounded-md border border-[#cfd7c8] bg-white p-6 text-xl font-semibold hover:bg-[#eef3e9]">Авторы</a>
        </div>
    </div>
@endsection
