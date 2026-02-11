<x-app-layout>
    <x-basic.content-page :title="__('Cadastrar Modelo de Nota')" :class="'card-secondary'" :back="route('nota-modelos.index')">
        <form method="POST" action="{{ route('nota-modelos.store') }}">
            @csrf
            @include('nota-modelos.form')
            <button type="submit" class="btn btn-primary">Salvar</button>
        </form>
    </x-basic.content-page>
</x-app-layout>
