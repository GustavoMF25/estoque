<div class="d-flex justify-content-around">
    @if (optional(auth()->user())->isSuperAdmin())
        @if ($row->status === 'pendente')
            <form action="{{ route('faturas.marcarPago', $row->id) }}" method="POST" onsubmit="return confirm('Marcar fatura como paga?')">
                @csrf
                @method('PUT')
                <button type="submit" class="btn btn-sm btn-success" title="Marcar como paga">
                    <i class="fas fa-check-circle"></i>
                </button>
            </form>
        @else
            <button class="btn btn-sm btn-outline-secondary" disabled title="Fatura já paga">
                <i class="fas fa-check-circle"></i>
            </button>
        @endif
    @endif
    <form action="{{ route('faturas.destroy', $row->id) }}" method="POST" onsubmit="return confirm('Excluir esta fatura?')" style="display:inline-block;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger">
            <i class="fas fa-trash"></i>
        </button>
    </form>
</div>
