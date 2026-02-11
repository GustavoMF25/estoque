<x-app-layout>
    <x-basic.content-page :title="__('Fabricantes')" :class="'card-secondary'" :btnCadastrarAdmin="['route' => route('fabricantes.create'), 'title' => 'Cadastrar Fabricante']">
        @livewire('fabricante-table')
    </x-basic.content-page>
</x-app-layout>
