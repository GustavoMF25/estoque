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
                                id: '$id'
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
                                nome: '$nome',
                                id: '$id'
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
        <div class='ml-2'>
            <button type='button' onclick=\"
                    window.dispatchEvent(new CustomEvent('abrirModal', {
                        detail: {
                            titulo: 'Retirar produto',
                            formId: 'removerProduto',
                            paramsBtn: 'wire:click=\\'remover\\'',
                            componente: 'produto.remover-produto',
                            props: { 
                                nome: '$nome',
                                id: '$id'
                            }
                        }
                    }));
                    $('#modal-sm').modal('show');
                    \"
                    class='btn btn-primary btn-block btn-sm'>
                    <i class='fa fa-minus'></i>
                    Retirar
            </button>
        </div>
";

@endphp

<x-app-layout>
    {{-- <x-basic.content-page-fluid>
        <div class="row">
            <div class="col-md-4"> --}}
                <x-basic.content-page :title="__('Produto')" :class="'card-secondary'" :btnAcoes="$btnAcoes" :size="'sm'">
<<<<<<< HEAD
                    <livewire:produto.produto-visualizar :id="$id" :nome="$nome" :estoque_id="$estoque_id"
=======
                    <livewire:produto.produto-visualizar :nome="$nome" :estoque_id="$estoque_id" :id="$id"
>>>>>>> e1bc09f (Refatoração de produtos, refatoração de Nota, nova exibição de produtos, 1 Produto n unidades, logo produto sendo acessado pelo ID, seeds para migração de nova formulação do banco, rodar apenas 1 vez, lembrar de fazer um backup antes de realizar as alterações)
                        wire:key="produto-visualizar" />
                </x-basic.content-page>
            {{-- </div>
        </div>
    </x-basic.content-page-fluid> --}}
</x-app-layout>
