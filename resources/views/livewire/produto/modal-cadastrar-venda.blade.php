<form action="{{ route('produtos.vender') }}" method="POST" id="formVenda">
    @csrf
    <div class="form-group">
        <label for="categoriaSelect">Categoria</label>
        <select id="categoriaSelect" class="form-control select2" wire:model="categoriaId" wire:change="categoriaSelecionada">
            <option value="">Sem categoria</option>
            @foreach ($categorias as $categoria)
            <option value="{{ $categoria->id }}">{{ $categoria->nome }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="produtoSelect">Produto</label>
        <select id="produtoSelect" name="nome" class="form-control select2">
            <option value="">Selecione um produto</option>
            @foreach ($produtos as $produto)
            <option value="{{ $produto->nome }}">
                {{ $produto->nome }} ({{ $produto->total ?? 0 }} disponíveis)
            </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="qtd">Quantidade Total a Vender</label>
        <input type="number" name="quantidade" id="qtd" class="form-control" min="1" required>
        @error('quantidade')
        <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
</form>