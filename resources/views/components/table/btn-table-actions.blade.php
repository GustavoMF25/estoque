<div class="d-flex">
    @if (!empty($remove) && auth()->user()->perfil === 'admin')
        <form action="{{ $remove['route'] }}" method="POST"
            onsubmit="return confirm('Deseja realmente excluir este registro?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger" title="Excluir">
                <i class="fas fa-trash-alt"></i>
            </button>
        </form>
    @endif

    @if (!empty($show))
        <x-table.btn-ver :title="$show['title']" :view="$show['view']" />

@endif


</div>
