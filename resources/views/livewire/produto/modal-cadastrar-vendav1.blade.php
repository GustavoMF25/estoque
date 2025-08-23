<form action="{{ route('produtos.vender') }}" method="POST" id="formVenda">
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
        <div class="mb-2">
            <label for="produto">Produto</label>
            <input type="text" wire:model="buscaProduto" wire:change="BuscaProduto" class="form-control" placeholder="Digite para buscar...">
        </div>

        <select name="nome" class="form-control" wire:model="produtoSelecionado" wire:change="ProdutoSelecionado">
            <option value="">Selecione um produto</option>
            @foreach ($produtos as $produto)
            <option value="{{ $produto->nome }}" {{$produtoSelecionado ?? 'selected'}} >
                {{ $produto->nome }} ({{ $produto->total ?? 0 }} disponíveis)
            </option>
            @endforeach
        </select>

        <br>
        <b>Selecionado: {{ $produtoSelecionado ?? ''}}</b>
    </div>


    <!-- Campo de Quantidade -->
    <div class="form-group">
        <label for="quantidade">Quantidade Total a Vender</label>
        <input type="number" name="quantidade" id="qtd" class="form-control" wire:model="quantidade" min="1" wire:change="verificaQuantidade" required>
        @error('quantidade')
        <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
</form>