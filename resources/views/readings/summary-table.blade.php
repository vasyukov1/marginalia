<div class="mb-5 overflow-auto rounded-xl border border-line" role="region" aria-label="{{ $heading }}" tabindex="0">
    <table class="w-full text-left text-sm">
        <caption class="bg-surface-raised px-4 py-3 text-left font-semibold">{{ $heading }}</caption>
        <thead class="bg-brand text-canvas">
            <tr>
                <th scope="col" class="px-4 py-3">{{ $column }}</th>
                <th scope="col" class="px-4 py-3 text-right">{{ __('Прочитано книг') }}</th>
                <th scope="col" class="px-4 py-3 text-right">{{ __('Страниц') }}</th>
                <th scope="col" class="px-4 py-3 text-right">{{ __('Средняя оценка') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $label => $row)
                <tr class="border-t border-line even:bg-surface-raised">
                    <th scope="row" class="px-4 py-3 font-medium">{{ $label === 'unknown' ? __('Год не указан') : __((string) $label) }}</th>
                    <td class="px-4 py-3 text-right tabular-nums text-accent">{{ $row['books'] }}</td>
                    <td class="px-4 py-3 text-right tabular-nums">{{ number_format($row['pages'], 0, ',', ' ') }}</td>
                    <td class="px-4 py-3 text-right tabular-nums">{{ $row['rating'] === null ? '—' : number_format($row['rating'], 1, app()->getLocale() === 'ru' ? ',' : '.').' / 10' }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-7 text-center text-muted">{{ __('Нет прочитанных книг за этот период') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
