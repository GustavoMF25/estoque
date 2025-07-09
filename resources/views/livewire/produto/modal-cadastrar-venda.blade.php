<form action="{{ route('produtos.vender') }}" id="formVenda" method="POST">
    @csrf
    <!-- Select de Categoria -->

    <div class="form-group">
        <label for="categoria">Categoria</label>
        <select name="categoria" class="form-control" wire:model="categoriaSelecionada">
            <option value="">Selecione uma categoria</option>
            @forelse ($categorias as $categoria)
            <option value="{{ $categoria->id }}">{{ $categoria->nome }}</option>
            @empty
            @endforelse
        </select>
    </div>

    <div class="form-group">
        <label for="produto">Produto</label>
        <select name="produto" id="selectProduto" class="form-control" wire:model="produtoSelecionado">
            <option value="">Selecione um produto</option>
            @foreach ($produtos as $produto)
            <option value="{{ $produto->id }}">
                {{ $produto->nome }} ({{ $produto->total }} disponíveis)
            </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="quantidade">Quantidade Total a Vender</label>
        <input type="number" name="quantidade" class="form-control" required min="1">
    </div>
</form>
<script>
    document.addEventListener('livewire:load', function() {
        $('#selectProduto').select2({
            placeholder: 'Selecione ou busque pelo nome',
            width: '100%'
        });

        $('#selectProduto').on('change', function() {
            let produtoId = $(this).val();
            Livewire.emit('produtoSelecionadoChanged', produtoId);
        });

        Livewire.hook('message.processed', () => {
            $('#selectProduto').select2('destroy').select2({
                placeholder: 'Selecione ou busque pelo nome',
                width: '100%'
            });
        });
    });
</script>