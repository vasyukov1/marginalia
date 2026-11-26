@extends('layouts.catalog')

@section('content')
    <div class="mx-auto max-w-3xl py-8">
        <a href="{{ route('readings.create') }}" class="inline-block primary-action">{{ __('Добавить прочитанную книгу') }}</a>
        <div class="mt-8 grid gap-4 sm:grid-cols-2">
            <a href="{{ route('books.index') }}" class="rounded-xl border border-line bg-surface p-6 text-xl font-semibold hover:bg-surface-raised">{{ __('Книги') }}</a>
            <a href="{{ route('authors.index') }}" class="rounded-xl border border-line bg-surface p-6 text-xl font-semibold hover:bg-surface-raised">{{ __('Авторы') }}</a>
        </div>
    </div>
@endsection
