<div class="d-flex justify-content-end mb-2">
    @if(auth()->user()->perfil === 'admin')
        <a href="{{ $route }}" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> {{$title}}
        </a>
    @endif
</div>
