@extends('layouts.catalog')

@section('content')
    <div class="mx-auto max-w-3xl py-8">
        <a href="{{ route('readings.create') }}" class="inline-block primary-action">{{ __('Добавить прочитанную книгу') }}</a>
        <div class="mt-8 grid gap-4 sm:grid-cols-2">
            <a href="{{ route('books.index') }}" class="rounded-xl border border-line bg-surface p-6 text-xl font-semibold hover:bg-surface-raised">{{ __('Библиотека') }}</a>
            <a href="{{ route('authors.index') }}" class="rounded-xl border border-line bg-surface p-6 text-xl font-semibold hover:bg-surface-raised">{{ __('Авторы') }}</a>
        </div>
        <div x-data="{ paused: false }" class="mt-8 text-center">
            <div class="book-animation" :class="{ 'is-paused': paused }" aria-hidden="true">
                <div class="animated-book">
                    <div class="book-back"></div>
                    <div class="book-paper book-paper-third"></div>
                    <div class="book-paper book-paper-second"></div>
                    <div class="book-paper book-paper-first"></div>
                    <div class="book-cover"><span>m.</span><i></i></div>
                </div>
            </div>
            <button type="button" @click="paused = !paused" :aria-pressed="paused"
                    class="rounded px-3 py-1 text-xs text-muted hover:text-link motion-reduce:hidden">
                <span x-text="paused ? @js(__('Продолжить анимацию')) : @js(__('Приостановить анимацию'))"></span>
            </button>
        </div>
    </div>
@endsection
