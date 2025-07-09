<x-app-layout>
    <x-basic.content-page :title="__('Categorias')" :class="'card-secondary'" :btnCadastrarAdmin="['route' => route('categorias.create'), 'title' => 'Cadastrar Categorias']">
        @livewire('categoria-table')
    </x-basic.content-page>
</x-app-layout>
