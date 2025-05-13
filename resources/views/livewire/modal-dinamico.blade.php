<div>
    <div wire:ignore.self class="modal fade modal-custom-center" id="modal-sm" tabindex="-1">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="titulo">{{ $titulo }}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body" id="body">
                    {!! $conteudo !!}
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        window.addEventListener('abrirModal', (event) => {
            const {
                titulo,
                conteudo
            } = event.detail;

            document.getElementById('titulo').innerHTML = titulo
            document.getElementById('body').innerHTML = conteudo
        });

        window.addEventListener('abrir-modal-bootstrap', () => {
            $('#modal-sm').modal('show');
        });

        window.addEventListener('fechar-bootstrap-modal', () => {
            $('#modal-sm').modal('hide');
        });
    </script>

</div>
