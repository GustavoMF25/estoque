<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn btn-block btn-outline-danger']) }}>
    {{ $slot }}
</button>
