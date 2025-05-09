<x-app-layout>
    <x-basic.content-page :title="__('Cadastrar Produto')" :class="'card-secondary'" :back="route('produtos.index')">
        <form action="{{ route('produtos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nome">Nome do Produto</label>
                    <input type="text" name="nome" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="codigo_barras">Código de Barras</label>
                    <input type="text" name="codigo_barras" class="form-control">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="unidade">Unidade</label>
                    <input type="text" name="unidade" class="form-control" value="un">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="preco">Preço</label>
                    <input type="number" name="preco" class="form-control" step="0.01">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="quantidade">Quantidade a Cadastrar</label>
                    <input type="number" name="quantidade" class="form-control" required min="1" value="1">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="imagem">Imagem do Produto</label>
                    <input type="file" name="imagem" class="form-control" accept="image/*">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="estoque_id">Estoque</label>
                    <select name="estoque_id" class="form-control" required>
                        <option value="">Selecione...</option>
                        @foreach ($estoques as $estoque)
                            <option value="{{ $estoque->id }}">{{ $estoque->nome }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="ativo">Ativo?</label>
                    <select name="ativo" class="form-control">
                        <option value="1">Sim</option>
                        <option value="0">Não</option>
                    </select>
                </div>
            </div>

            <x-button.save>
                {{ __('Saved.') }}
            </x-button.save>
        </form>
    </x-basic.content-page>
</x-app-layout>
