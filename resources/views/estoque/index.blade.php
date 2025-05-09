<x-app-layout>
    <x-basic.content-page :title="__('Estoque')" :class="'card-secondary'" :btnCadastrarAdmin="['route' => route('estoques.create'), 'title' => 'Cadastrar Estoque']">
        @livewire('estoque-table')
    </x-basic.content-page>
</x-app-layout>
