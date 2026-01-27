<div class="d-flex justify-content-around">

    @if (!empty($show))
        <x-table.btn-ver :title="$show['title']" :componente="$show['componente']" :props="$show['props']" :modal="$show['modal']" :route="$show['route']" />
    @endif

    @if (!empty($edit))
        @if ((!empty($edit) && auth()->user()->perfil === 'admin') || $edit['permitir'])
            @if (!empty($edit['route']))
                <a href="{{ $edit['route'] }}" class="btn btn-sm btn-warning" title="{{ $edit['title'] ?? 'Editar' }}">
                    <i class="fas fa-pen"></i>
                </a>
            @else
                <x-table.btn-edit :title="$edit['title']" :componente="$edit['componente']" :props="$edit['props']" :formId="$edit['formId']" />
            @endif
        @endif
    @endif
    @if (!empty($custonComponents) && is_array($custonComponents))
        @foreach ($custonComponents as $custonItem)
            @if ((!empty($custonItem) && auth()->user()->perfil === 'admin') || $custonItem['permitir'])
                <x-table.btn-custon-component :icon="$custonItem['icon']" :title="$custonItem['title']" :componente="$custonItem['componente']" :props="$custonItem['props']" :formId="$custonItem['formId']" />
            @endif
        @endforeach
    @elseif (!empty($custonComponent))
        @if ((!empty($custonComponent) && auth()->user()->perfil === 'admin') || $custonComponent['permitir'])
            <x-table.btn-custon-component :icon="$custonComponent['icon']" :title="$custonComponent['title']" :componente="$custonComponent['componente']" :props="$custonComponent['props']" :formId="$custonComponent['formId']" />
        @endif
    @endif

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


    @if (!empty($restore))
        <form action="{{ $restore['route'] }}" method="POST"
            onsubmit="return confirm('Deseja realmente restaurar este registro?')">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-sm btn-info" title="Restaurar">
                <i class="fas fa-sync-alt"></i>
            </button>
        </form>
    @endif

    @if (!empty($pdf))
        <a href="{{ $pdf['route'] }}" target="_blank" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-file-pdf"></i>
        </a>
    @endif


</div>
