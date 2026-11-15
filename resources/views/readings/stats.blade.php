<dl class="mb-6 flex flex-wrap gap-x-10 gap-y-4 border-b border-[#dce1d7] pb-5">
    <div><dt class="text-xs text-[#687362]">{{ __('Прочитано книг') }}</dt><dd class="mt-1 text-2xl font-semibold tabular-nums">{{ $stats['books'] }}</dd></div>
    @unless ($authorStats ?? false)
        <div><dt class="text-xs text-[#687362]">{{ __('Авторов') }}</dt><dd class="mt-1 text-2xl font-semibold tabular-nums">{{ $stats['authors'] }}</dd></div>
    @endunless
    <div><dt class="text-xs text-[#687362]">{{ __('Страниц') }}</dt><dd class="mt-1 text-2xl font-semibold tabular-nums">{{ number_format($stats['pages'], 0, ',', ' ') }}</dd></div>
    <div><dt class="text-xs text-[#687362]">{{ __('Средняя оценка') }}</dt><dd class="mt-1 text-2xl font-semibold tabular-nums">{{ $stats['rating'] !== null ? number_format($stats['rating'], 1, app()->getLocale() === 'ru' ? ',' : '.').' / 10' : '—' }}</dd></div>
</dl>
