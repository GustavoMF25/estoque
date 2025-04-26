@if ($errors->any())

    @foreach ($errors->all() as $error)
        <div class="callout callout-danger m-2">
            <h5>{{ __('Whoops! Something went wrong.') }}</h5>
            <p>{{ $error }}</p>
        </div>
    @endforeach
@endif
