<div>
    <div wire:ignore.self class="modal fade modal-custom-center" id="modal-sm" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="titulo">{{ $titulo }}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body" id="body">
                    {!! $conteudo !!}
                </div>
                <div class="modal-footer">
                    @if (!empty($formId))
                        <button id="btnSalvar" class="btn btn-success" form="{{ $formId }}">Salvar</button>
                    @endif
                    @if(!empty($paramsBtn))
                        <button id="btnSalvar" class="btn btn-success" {{$paramsBtn}}>Salvar</button>
                    @endif
                    <button class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
</div>