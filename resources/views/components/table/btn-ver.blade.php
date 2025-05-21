@props(['title', 'componente','props'])

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
    class="btn btn-sm btn-info">
    <i class="fas fa-eye"></i>
</button>
