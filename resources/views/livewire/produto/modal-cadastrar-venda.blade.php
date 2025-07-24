<form wire:submit.prevent="venderProduto" id="formVenda">
    @csrf

    <!-- Select de Categoria -->
    <div class="form-group">
        <label for="categoria">Categoria</label>
        <select class="form-control select2" wire:model="categoriaId" wire:change="categoriaSelecionada">
            <option value="">Sem categoria</option>
            @foreach ($categorias as $categoria)
                <option value="{{ $categoria->id }}">{{ $categoria->nome }}</option>
            @endforeach
        </select>
    </div>

    <!-- Select de Produto -->
    <div class="form-group">
        <label for="produto">Produto</label>
        <select class="form-control" wire:model="produtoSelecionado">
            <option value="">Selecione um produto</option>
            @foreach ($produtos as $produto)
                <option value="{{ $produto->nome }}">
                    {{ $produto->nome }} ({{ $produto->total ?? 0 }} disponíveis)
                </option>
            @endforeach
        </select>
    </div>

    <!-- Campo de Quantidade -->
    <div class="form-group">
        <label for="quantidade">Quantidade Total a Vender</label>
        <input type="number" class="form-control" wire:model="quantidade" min="1" required>
    </div>
</form>