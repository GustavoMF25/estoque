@props(['title', 'view'])

<button
    onclick="
        window.dispatchEvent(new CustomEvent('abrirModal', {
             detail: {
                titulo: {{ \Illuminate\Support\Js::from($title) }},
                componente: {{ \Illuminate\Support\Js::from($componente) }},
                props: {{ \Illuminate\Support\Js::from($props) }}
            }
        }));
        $('#modal-sm').modal('show');
    "
    class="btn btn-sm btn-warning">
   <i class="fas fa-pen"></i>
</button>
