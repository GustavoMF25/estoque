<x-app-layout>
    <x-basic.content-page :class="'card-secondary'" :title="__('Editar Perfil')">
        <form action="{{ route('perfis.update', $perfil) }}" method="POST">
            @csrf
            @method('PUT')
            @include('perfis._form')

            <div class="mt-4">
                <button type="submit" class="btn btn-success">Atualizar</button>
                <a href="{{ route('perfis.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </x-basic.content-page>
</x-app-layout>
