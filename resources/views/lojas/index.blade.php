<x-app-layout>
    <x-basic.content-page :title="__('Lojas')" :class="'card-secondary'" :btnCadastrarAdmin="['route' => route('lojas.create'), 'title' => 'Cadastrar Loja']">
        @livewire('loja-table')
    </x-basic.content-page>
</x-app-layout>