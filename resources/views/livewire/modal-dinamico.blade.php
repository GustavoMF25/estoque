<div>
    <div wire:ignore.self class="modal fade modal-custom-center" id="modal-sm" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="titulo">{{ $titulo }}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body" id="body">

                </div>
                <div class="modal-footer">

                    <button id="btnSalvar" class="btn btn-success d-none">Salvar</button>
                    <button class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        window.addEventListener('abrirModal', (event) => {
            const {
                titulo,
                conteudo,
                formId
            } = event.detail;

            document.getElementById('titulo').innerHTML = titulo
            document.getElementById('body').innerHTML = conteudo
            const btnSalvar = document.getElementById('btnSalvar');

            if (formId) {
                btnSalvar.classList.remove('d-none');
                btnSalvar.setAttribute('form', formId);
            }
            
            Livewire.restart();
        });

        window.addEventListener('abrir-modal-bootstrap', () => {
            $('#modal-sm').modal('show');
        });

        window.addEventListener('fechar-bootstrap-modal', () => {
            $('#modal-sm').modal('hide');
        });
    </script>

</div>