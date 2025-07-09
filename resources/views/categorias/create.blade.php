<x-app-layout>
    <x-basic.content-page :title="__('Cadastrar Estoque')" :class="'card-secondary'" :back="route('estoques.index')">
        <form method="POST" action="{{ route('categorias.store') }}">
            @csrf

            <div class="form-group">
                <label>Nome</label>
                <input type="text" name="nome" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Descrição</label>
                <textarea name="descricao" class="form-control"></textarea>
            </div>

            <div class="form-group">
                <label>Ativo</label>
                <select name="ativo" class="form-control" required>
                    <option value="1" selected>Sim</option>
                    <option value="0">Não</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Criar Categoria</button>
        </form>
    </x-basic.content-page>
</x-app-layout>