<button {{ $attributes->merge(['type' => 'button', 'class' => 'secondary-action disabled:opacity-50']) }}>
    {{ $slot }}
</button>
