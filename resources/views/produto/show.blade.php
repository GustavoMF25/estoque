<x-app-layout>
    <x-basic.content-page :title="__('Produto')" :class="'card-secondary'" :btnCadastrarAdmin="['route' => route('produtos.create'), 'title' => 'Cadastrar Produto']">
        <livewire:produto.produto-visualizar :key="now().'produto-visualizar'" :nome="$nome" :estoque_id="$estoque_id" :ultima_movimentacao="$ultima_movimentacao" />
    </x-basic.content-page>
</x-app-layout>
