<x-app-layout>
    <x-basic.content-page :title="__('Cadastrar Fabricante')" :class="'card-secondary'" :back="route('fabricantes.index')">
        <form method="POST" action="{{ route('clientes.store') }}">
            @csrf
            @include('clientes.form')
            <x-button.save>
                {{ __('Saved.') }}
            </x-button.save>
        </form>
    </x-basic.content-page>
</x-app-layout>
