<div class="d-flex justify-content-around">
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
        <x-table.btn-ver :title="$show['title']" :componente="$show['componente']" :props="$show['props']" />
    @endif

    @if (!empty($edit))
        <x-table.btn-edit :title="$edit['title']" :view="$edit['view']" />
    @endif



    @if (!empty($restore))
        <form action="{{ $restore['route'] }}" method="POST"
            onsubmit="return confirm('Deseja realmente restalrar este registro?')">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-sm btn-info" title="Restaurar">
                <i class="fas fa-sync-alt"></i>
            </button>
        </form>
    @endif


</div>
