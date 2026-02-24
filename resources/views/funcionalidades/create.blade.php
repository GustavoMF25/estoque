<x-app-layout>
    <x-basic.content-page :class="'card-secondary'" :title="__('Cadastrar Funcionalidade')">
        <form action="{{ route('funcionalidades.store') }}" method="POST">
            @csrf
            @include('funcionalidades._form')

            <div class="mt-4">
                <button type="submit" class="btn btn-success">Salvar</button>
                <a href="{{ route('funcionalidades.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </x-basic.content-page>
</x-app-layout>
