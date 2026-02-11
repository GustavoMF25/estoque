<div>
    <form action="{{ route('produtos.store') }}" id="cadastrarProduto" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="nome">Nome do Produto</label>
                <input type="text" name="nome" class="form-control" required>
            </div>
            <div class="col-md-4 mb-3">
                <label for="valor_entrada">Valor de entrada</label>
                <input type="number" name="valor_entrada" class="form-control" step="0.01" required>
            </div>
            <div class="col-md-4 mb-3">
                <label for="valor_venda">Valor de venda</label>
                <input type="number" name="valor_venda" class="form-control" step="0.01" required>
            </div>

            <div class="col-md-4 mb-3">
                <label for="quantidade">Quantidade a Cadastrar</label>
                <input type="number" name="quantidade" class="form-control" required min="1" value="1">
            </div>
            <div class="col-md-3 mb-3">
                <label for="imagem">Imagem do Produto</label>
                <input type="file" name="imagem" class="form-control" accept="image/*">
            </div>

            <div class="col-md-3 mb-3">
                <label for="estoque_id">Estoque</label>
                <select name="estoque_id" class="form-control" required>
                    <option value="">Selecione...</option>
                    @foreach ($estoques as $estoque)
                    <option value="{{ $estoque->id }}">{{ $estoque->nome }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Select de Categoria -->
            <div class="form-group col-md-3 mb-3">
                <label for="categoria">Categoria</label>
                <select name="categoria_id" class="form-control select2">
                    <option value="">Sem categoria</option>
                    @forelse ($categorias as $categoria)
                    <option value="{{ $categoria->id }}">{{ $categoria->nome }}</option>
                    @empty
                    @endforelse
                </select>
            </div>
            <div class="form-group col-md-3 mb-3">
                <label for="fabrricante">Fabricante</label>
                <select name="fabrricante_id" class="form-control select2">
                    <option value="">Sem fabricantes</option>
                    @forelse ($fabricantes as $fabricante)
                    <option value="{{ $fabricante->id }}">{{ $fabricante->nome }}</option>
                    @empty
                    @endforelse
                </select>
            </div>

        </div>
    </form>
</div>
