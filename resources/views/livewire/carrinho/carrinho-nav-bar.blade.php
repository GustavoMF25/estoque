<div>
    <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="fas fa-shopping-cart"></i>
        @if($qtdItemCarrinho)
        <span class="badge badge-warning navbar-badge">{{ $qtdItemCarrinho }}</span>
        @endif
    </a>

    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <span class="dropdown-item dropdown-header">{{ $qtdItemCarrinho }} Itens no Carrinho</span>
        <div class="dropdown-divider"></div>


        @forelse ($itens as $item)
        <div class="dropdown-item d-flex align-items-center">
            <img src="{{ asset('storage/' . $item['imagem']) }}" class="img-size-50 mr-2 rounded"
                style="width: 40px; height: 40px; object-fit: cover;">
            <div class="flex-grow-1">
                <div class="fw-bold">{{ $item['nome'] }}</div>
                <small>{{ $item['quantidade'] }}x R$
                    {{ App\Helpers\FormatHelper::brl($item['preco_unitario']) }}</small>
            </div>
            <button wire:click="removerItem('{{ $item['nome'] }}')" class="btn btn-sm btn-outline-danger ml-2" title="Remover item">
            <i class="fas fa-trash-alt"></i>
        </button>
        </div>
        

        <div class="dropdown-divider"></div>
        @empty
        <div class="dropdown-item text-muted text-center">
            Carrinho vazio 🛒
        </div>
        @endforelse

        <a href="#" class="dropdown-item dropdown-footer">
            Confirmar compra
        </a>
    </div>
</div>