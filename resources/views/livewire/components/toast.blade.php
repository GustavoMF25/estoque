<div class="content mx-5 mt-2">
    @if ($message)
        <div class="alert alert-{{ $type == 'error' ? 'danger' : $type }} alert-dismissible fade show" role="alert"
            tabindex="-1" x-init="$el.focus()" x-data>
            <h5>
                @if ($type === 'success')
                    <i class="icon fas fa-check"></i>
                @elseif ($type === 'error' || $type === 'danger')
                    <i class="icon fas fa-ban"></i>
                @elseif ($type === 'warning')
                    <i class="icon fas fa-exclamation-triangle"></i>
                @else
                    <i class="icon fas fa-info"></i>
                @endif
                {{ ucfirst($type) }}
            </h5>
            {{ $message }}
            <button type="button" class="close" wire:click="dismiss" aria-label="Fechar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

</div>
