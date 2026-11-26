@props(['author'])

@if ($author->avatar_url)
    <img src="{{ $author->avatar_url }}" alt="{{ $author->localized_name }}" loading="lazy"
         {{ $attributes->merge(['class' => 'shrink-0 rounded-xl border border-line object-cover']) }}>
@else
    <span aria-hidden="true" {{ $attributes->merge(['class' => 'inline-flex shrink-0 items-center justify-center rounded-xl border border-line bg-surface-hover font-semibold text-brand']) }}>
        {{ mb_substr($author->localized_name, 0, 1) }}
    </span>
@endif
