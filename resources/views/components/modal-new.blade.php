<dialog id="{{ $id ?? 'modal' }}" tabindex="-1" role="dialog" style="padding: 0; border: none;">
    <div class="modal-dialog {{ $size ?? '' }}" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $title ?? 'Título' }}</h5>
                <button type="button" class="close" onclick="document.getElementById('{{ $id ?? 'modal' }}').close()" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{ $slot }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('{{ $id ?? 'modal' }}').close()">Cancelar</button>
                @if (!empty($confirmButton))
                    <button type="button" id="{{ $confirmButton['id'] }}" class="btn btn-primary">
                        {{ $confirmButton['label'] }}
                    </button>
                @endif
            </div>
        </div>
    </div>
</dialog>
