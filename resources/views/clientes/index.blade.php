<x-app-layout>
    <x-basic.content-page :title="__('Clientes')" :class="'card-secondary'" :btnCadastrarAdmin="['route' => route('clientes.create'), 'title' => 'Cadastrar Cliente']">
        <livewire:cliente-table :key="now() . 'cliente-table'" />
    </x-basic.content-page>
</x-app-layout>
