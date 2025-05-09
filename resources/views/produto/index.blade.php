<x-app-layout>
    <x-basic.content-page :title="__('Produto')" :class="'card-secondary'" :btnCadastrarAdmin="['route' => route('produtos.create'), 'title' => 'Cadastrar Produto']">
        @livewire('produto-table')
    </x-basic.content-page>
</x-app-layout>
