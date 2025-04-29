@props(['disabled' => false, 'icon' => null])


@if (empty($icon))
    <input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'form-control']) !!}>
@endif

@if (!empty($icon))
    <div class="input-group mb-3">
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="{{ $icon }}"></span>
            </div>
        </div>
        <input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'form-control']) !!}>

    </div>
@endif
