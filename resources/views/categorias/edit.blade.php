<x-app-layout>
    <x-basic.content-page :title="__('Editar Categoria')" :class="'card-secondary'" :back="route('categorias.index')">
        <form method="POST" action="{{ route('categorias.update', $categoria->id) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Nome</label>
                <input type="text" name="nome" class="form-control" value="{{ $categoria->nome }}" required>
            </div>

            <div class="form-group">
                <label>Descrição</label>
                <textarea name="descricao" class="form-control">{{ $categoria->descricao }}</textarea>
            </div>

            <div class="form-group">
                <label>Limite de venda padrão</label>
                <input type="number" name="limite_venda_padrao" class="form-control" min="1"
                    value="{{ $categoria->limite_venda_padrao }}">
                <small class="text-muted">Deixe vazio para não exigir aprovação.</small>
            </div>

            <div class="form-group">
                <label>Ativo</label>
                <select name="ativo" class="form-control" required>
                    <option value="1" {{ $categoria->ativo ? 'selected' : '' }}>Sim</option>
                    <option value="0" {{ !$categoria->ativo ? 'selected' : '' }}>Não</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Salvar</button>
        </form>
    </x-basic.content-page>
</x-app-layout>
