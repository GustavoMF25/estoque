<form action="{{ route('produtos.vender') }}" id="formVenda" method="POST">
    @csrf

    <div class="form-group">
        <label for="nome">Nome do Produto</label>
        <select name="nome" class="form-control" required>
            <option value="">Selecione um produto</option>
            @foreach ($produtos as $produto)
                <option value="{{ $produto->nome }}">
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
