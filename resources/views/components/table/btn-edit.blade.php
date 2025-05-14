@props(['title', 'view'])

<button
    onclick="
        window.dispatchEvent(new CustomEvent('abrirModal', {
            detail: {
                titulo: {{ \Illuminate\Support\Js::from($title) }},
                conteudo: {{ \Illuminate\Support\Js::from($view) }}
            }
        }));
        $('#modal-sm').modal('show');
    "
    class="btn btn-sm btn-warning">
   <i class="fas fa-pen"></i>
</button>
