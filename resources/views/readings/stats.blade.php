<dl class="reading-stats mb-7 grid grid-cols-2 gap-5 border-y border-line py-5 sm:grid-cols-4">
    <div><dt class="text-xs text-muted">{{ __('Прочитано книг') }}</dt><dd class="mt-1 text-3xl font-medium text-accent tabular-nums">{{ $stats['books'] }}</dd></div>
    @unless ($authorStats ?? false)
        <div><dt class="text-xs text-muted">{{ __('Авторов') }}</dt><dd class="mt-1 text-3xl font-medium text-accent tabular-nums">{{ $stats['authors'] }}</dd></div>
    @endunless
    <div><dt class="text-xs text-muted">{{ __('Страниц') }}</dt><dd class="mt-1 text-3xl font-medium text-accent tabular-nums">{{ number_format($stats['pages'], 0, ',', ' ') }}</dd></div>
    <div><dt class="text-xs text-muted">{{ __('Средняя оценка') }}</dt><dd class="mt-1 text-3xl font-medium text-accent tabular-nums">{{ $stats['rating'] !== null ? number_format($stats['rating'], 1, app()->getLocale() === 'ru' ? ',' : '.').' / 10' : '—' }}</dd></div>
</dl>
