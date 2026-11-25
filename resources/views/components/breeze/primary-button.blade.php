<button {{ $attributes->merge(['type' => 'submit', 'class' => 'primary-action disabled:opacity-50']) }}>
    {{ $slot }}
</button>
