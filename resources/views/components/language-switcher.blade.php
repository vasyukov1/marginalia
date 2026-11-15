<form method="POST" action="{{ route('locale.update') }}" class="flex items-center gap-2 text-sm" aria-label="{{ __('Язык') }}">
    @csrf
    @foreach (['ru' => 'RU', 'en' => 'EN'] as $locale => $label)
        <button name="locale" value="{{ $locale }}" lang="{{ $locale }}" aria-pressed="{{ app()->getLocale() === $locale ? 'true' : 'false' }}"
                @class(['rounded px-2 py-1', 'bg-[#e9eee4] font-semibold text-[#36583c]' => app()->getLocale() === $locale, 'text-[#687362] hover:underline' => app()->getLocale() !== $locale])>{{ $label }}</button>
    @endforeach
</form>
