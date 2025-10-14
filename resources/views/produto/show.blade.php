@php
    $btnAcoes = "
        <div>
            <button type='button'
                onclick=\"
                    window.dispatchEvent(new CustomEvent('abrirModal', {
                        detail: {
                            titulo: 'Atualizar produto',
                            formId: 'atualizarProduto',
                            paramsBtn: 'wire:click=\\'atualizar\\'',
                            componente: 'produto.modal-atualizar-produto',
                            props: { 
                                nome: '$nome',
                                ultimaMovimentacao: '$ultima_movimentacao'
                            }
                        }
                    }));
                    $('#modal-sm').modal('show');
                \"
                class='btn btn-warning btn-block btn-sm'>
                <i class='fa fa-pencil-alt'></i>
                Atualizar
            </button>
        </div>
        <div class='ml-2'>
            <button type='button'
                onclick=\"
                    window.dispatchEvent(new CustomEvent('abrirModal', {
                        detail: {
                            titulo: 'Adicionar produto',
                            formId: 'adicionarProduto',
                            paramsBtn: 'wire:click=\\'adicionar\\'',
                            componente: 'produto.modal-adicionar-produto',
                            props: { 
                                nome: '$nome'
                            }
                        }
                    }));
                    $('#modal-sm').modal('show');
                \"
                class='btn btn-primary btn-block btn-sm'>
                <i class='fa fa-plus'></i>
                Adicionar
            </button>
        </div>
";

@endphp

<x-app-layout>
    {{-- <x-basic.content-page-fluid>
        <div class="row">
            <div class="col-md-4"> --}}
                <x-basic.content-page :title="__('Produto')" :class="'card-secondary'" :btnAcoes="$btnAcoes" :size="'sm'">
                    <livewire:produto.produto-visualizar :nome="$nome" :estoque_id="$estoque_id" :ultima_movimentacao="$ultima_movimentacao"
                        wire:key="produto-visualizar" />
                </x-basic.content-page>
            {{-- </div>
        </div>
    </x-basic.content-page-fluid> --}}
</x-app-layout>
