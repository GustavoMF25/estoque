@props(['for'])

@error($for)
    <p {{ $attributes->merge(['class' => 'text-center text-danger']) }}>{{ $message }}</p>
@enderror
