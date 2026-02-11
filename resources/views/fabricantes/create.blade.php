<x-app-layout>
    <x-basic.content-page :title="__('Cadastrar Fabricante')" :class="'card-secondary'" :back="route('fabricantes.index')">
        <form method="POST" action="{{ route('fabricantes.store') }}">
            @csrf

            <div class="form-group">
                <label>Nome</label>
                <input type="text" name="nome" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Site</label>
                <textarea name="site" class="form-control"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Criar Fabricante</button>
        </form>
    </x-basic.content-page>
</x-app-layout>