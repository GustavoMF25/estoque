<x-app-layout>
    <x-basic.content-page :title="__('Editar Modelo de Nota')" :class="'card-secondary'" :back="route('nota-modelos.index')">
        <form method="POST" action="{{ route('nota-modelos.update', $modelo->id) }}">
            @csrf
            @method('PUT')
            @include('nota-modelos.form', ['modelo' => $modelo])
            <button type="submit" class="btn btn-primary">Salvar</button>
        </form>
    </x-basic.content-page>
</x-app-layout>
