<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-block btn-outline-primary']) }}>
    {{ $slot }}
</button>