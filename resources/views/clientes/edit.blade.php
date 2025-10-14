<x-app-layout>
    <x-basic.content-page :title="__('Cadastrar Fabricante')" :class="'card-secondary'" :back="route('fabricantes.index')">
        <form method="POST" action="{{ route('clientes.update', $cliente) }}">
            @csrf @method('PUT')
            @include('clientes.form', ['cliente' => $cliente])
            <button class="btn btn-primary">Atualizar</button>
        </form>
    </x-basic.content-page>
</x-app-layout>
