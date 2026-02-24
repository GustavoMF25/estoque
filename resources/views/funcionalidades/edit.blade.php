<x-app-layout>
    <x-basic.content-page :class="'card-secondary'" :title="__('Editar Funcionalidade')">
        <form action="{{ route('funcionalidades.update', $funcionalidade) }}" method="POST">
            @csrf
            @method('PUT')
            @include('funcionalidades._form')

            <div class="mt-4">
                <button type="submit" class="btn btn-success">Atualizar</button>
                <a href="{{ route('funcionalidades.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </x-basic.content-page>
</x-app-layout>
