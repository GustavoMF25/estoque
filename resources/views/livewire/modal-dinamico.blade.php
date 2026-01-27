<div>
    <div wire:ignore.self class="modal fade modal-custom-center" id="modal-sm" tabindex="-1">
        <div class="modal-dialog {{ $size }} modal-dialog-centered">
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
                    @if (!empty($paramsBtn))
                        <button id="btnSalvar" class="btn btn-success" {{ $paramsBtn }}>Salvar</button>
                    @endif
                    <button class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('initSelect2', () => {
                setTimeout(() => {
                    const $selects = $('.modal-content').find('select.select2');
                    if ($selects.length > 0) {
                        $selects.each(function() {
                            const $select = $(this);
                            if ($select.data('select2')) {
                                return;
                            }
                            $select.select2({
                            width: 'resolve',
                            // theme: "classic"
                            });
                        });

                        $selects.off('change.select2-lw').on('change.select2-lw', function() {
                            const $select = $(this);
                            const wireModel = $select.attr('wire:model')
                                || $select.attr('wire:model.defer')
                                || $select.attr('wire:model.lazy');
                            if (!wireModel) {
                                return;
                            }
                            const componentEl = this.closest('[wire\\:id]');
                            if (!componentEl) {
                                return;
                            }
                            const component = Livewire.find(componentEl.getAttribute('wire:id'));
                            if (!component) {
                                return;
                            }
                            component.set(wireModel, $select.val(), false);
                        });
                    }
                }, 50); // espera 50ms
            });
            Livewire.on('fecharModal', () => {
                $(".modal").modal('hide')
            });
        });
    </script>
@endpush
