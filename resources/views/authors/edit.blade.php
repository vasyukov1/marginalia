@extends('layouts.catalog')

@section('title', __('Редактировать автора'))

@section('content')
    <div class="mx-auto max-w-3xl">
        <a href="{{ route('authors.show', $author) }}" class="text-sm text-muted underline underline-offset-4">{{ __('Назад к автору') }}</a>
        <h1 class="mt-5 mb-6 text-2xl font-semibold">{{ __('Редактировать автора') }}</h1>
        <form method="POST" action="{{ route('authors.update', $author) }}" class="space-y-5 rounded-xl border border-line bg-surface p-5 sm:p-7">
            @csrf
            @method('PATCH')
            @if ($errors->any())
                <ul role="alert" class="space-y-1 text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
            <div>
                <label for="name" class="mb-2 block text-sm">{{ __('Автор на русском *') }}</label>
                <input id="name" name="name" required maxlength="255" value="{{ old('name', $author->name) }}" class="table-filter w-full">
            </div>
            <div>
                <label for="name_en" class="mb-2 block text-sm">{{ __('Автор на английском') }}</label>
                <input id="name_en" name="name_en" maxlength="255" value="{{ old('name_en', $author->name_en) }}" class="table-filter w-full">
            </div>
            <div>
                <label for="country" class="mb-2 block text-sm">{{ __('Страна автора') }}</label>
                <input id="country" name="country" maxlength="100" value="{{ old('country', $author->country) }}" class="table-filter w-full">
            </div>
            <div class="flex flex-wrap gap-4">
                <button class="primary-action">{{ __('Сохранить') }}</button>
                <a href="{{ route('authors.show', $author) }}" class="secondary-action">{{ __('Отмена') }}</a>
            </div>
        </form>
    </div>
@endsection
