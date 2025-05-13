<div wire:ignore.self class="modal fade" id="modal-sm" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true" x-data x-show="$wire.modalAberto" @modal-fechar.window="$wire.fecharModal()">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">{{ $modalTitulo }}</h5>
                <button type="button" class="close" wire:click="fecharModal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                {!! $modalConteudo !!}
            </div>

            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-secondary" wire:click="fecharModal">Fechar</button>
                {{-- Botão opcional --}}
                <button type="button" class="btn btn-primary">Salvar</button>
            </div>

        </div>
    </div>
</div>
