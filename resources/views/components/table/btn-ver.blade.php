@props(['title', 'componente', 'props', 'formId', 'modal', 'route'])

@if ($modal)
    <button
        onclick="
        window.dispatchEvent(new CustomEvent('abrirModal', {
            detail: {
                titulo: {{ \Illuminate\Support\Js::from($title) }},
                componente: {{ \Illuminate\Support\Js::from($componente) }},
                props: {{ \Illuminate\Support\Js::from($props) }},
                formId: {{ \Illuminate\Support\Js::from(!empty($formId) ? $formId : null) }}
            }
        }));
        $('#modal-sm').modal('show');
    "
        class="btn btn-sm btn-info">
        <i class="fas fa-eye"></i>
    </button>
@endif

@if (!$modal)
    <a href="{{ $route }}" class="btn btn-sm btn-info">
        <i class="fas fa-eye"></i>
    </a>
@endif
