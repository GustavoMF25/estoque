<x-app-layout>
    <x-basic.content-page :title="__('Produto')" :class="'card-secondary'">
        <livewire:produto.produto-visualizar
            :nome="$nome"
            :estoque_id="$estoque_id"
            :ultima_movimentacao="$ultima_movimentacao"
            wire:key="produto-visualizar" />
    </x-basic.content-page>
</x-app-layout>