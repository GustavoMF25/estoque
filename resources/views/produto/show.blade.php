@php
    $btnAcoes = "
        <div class='d-none d-sm-flex gap-2 flex-wrap'>
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
                                id: '$id',
                                size: 'modal-lg'
                            }
                        }
                    }));
                    $('#modal-sm').modal('show');
                \"
                class='btn btn-warning btn-sm'>
                <i class='fa fa-pencil-alt'></i>
                Atualizar
            </button>
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
                                id: '$id',
                                size: 'modal-lg'
                            }
                        }
                    }));
                    $('#modal-sm').modal('show');
                \"
                class='btn btn-primary btn-sm'>
                <i class='fa fa-plus'></i>
                Adicionar
            </button>
            <button type='button' onclick=\"
                    window.dispatchEvent(new CustomEvent('abrirModal', {
                        detail: {
                            titulo: 'Retirar produto',
                            formId: 'removerProduto',
                            paramsBtn: 'wire:click=\\'remover\\'',
                            componente: 'produto.remover-produto',
                            props: { 
                                nome: '$nome',
                                id: '$id',
                                size: 'modal-lg'
                            }
                        }
                    }));
                    $('#modal-sm').modal('show');
                    \"
                    class='btn btn-primary btn-sm'>
                    <i class='fa fa-minus'></i>
                    Retirar
            </button>
            <form action='" . route('produtos.desativar', $id) . "' method='POST'
                onsubmit=\"return confirm('Deseja realmente desativar este produto?')\">
                " . csrf_field() . method_field('PATCH') . "
                <button type='submit' class='btn btn-secondary btn-sm'>
                    <i class='fa fa-ban'></i>
                    Desativar
                </button>
            </form>
            <form action='" . route('produtos.destroy', $id) . "' method='POST'
                onsubmit=\"return confirm('Deseja realmente excluir este produto?')\">
                " . csrf_field() . method_field('DELETE') . "
                <button type='submit' class='btn btn-danger btn-sm'>
                    <i class='fa fa-trash'></i>
                    Excluir
                </button>
            </form>
        </div>

        <div class='d-flex d-sm-none'>
            <div class='dropdown w-100'>
                <button class='btn btn-secondary btn-sm dropdown-toggle w-100' type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                    Acoes do produto
                </button>
                <div class='dropdown-menu w-100'>
                    <button class='dropdown-item'
                        onclick=\"
                            window.dispatchEvent(new CustomEvent('abrirModal', {
                                detail: {
                                    titulo: 'Atualizar produto',
                                    formId: 'atualizarProduto',
                                    paramsBtn: 'wire:click=\\'atualizar\\'',
                                    componente: 'produto.modal-atualizar-produto',
                                    props: { 
                                        nome: '$nome',
                                        id: '$id',
                                        size: 'modal-lg'
                                    }
                                }
                            }));
                            $('#modal-sm').modal('show');
                        \">
                        <i class='fa fa-pencil-alt mr-2'></i>Atualizar
                    </button>
                    <button class='dropdown-item'
                        onclick=\"
                            window.dispatchEvent(new CustomEvent('abrirModal', {
                                detail: {
                                    titulo: 'Adicionar produto',
                                    formId: 'adicionarProduto',
                                    paramsBtn: 'wire:click=\\'adicionar\\'',
                                    componente: 'produto.modal-adicionar-produto',
                                    props: { 
                                        nome: '$nome',
                                        id: '$id',
                                        size: 'modal-lg'
                                    }
                                }
                            }));
                            $('#modal-sm').modal('show');
                        \">
                        <i class='fa fa-plus mr-2'></i>Adicionar
                    </button>
                    <button class='dropdown-item'
                        onclick=\"
                            window.dispatchEvent(new CustomEvent('abrirModal', {
                                detail: {
                                    titulo: 'Retirar produto',
                                    formId: 'removerProduto',
                                    paramsBtn: 'wire:click=\\'remover\\'',
                                    componente: 'produto.remover-produto',
                                    props: { 
                                        nome: '$nome',
                                        id: '$id',
                                        size: 'modal-lg'
                                    }
                                }
                            }));
                            $('#modal-sm').modal('show');
                        \">
                        <i class='fa fa-minus mr-2'></i>Retirar
                    </button>
                    <form action='" . route('produtos.desativar', $id) . "' method='POST'
                        onsubmit=\"return confirm('Deseja realmente desativar este produto?')\">
                        " . csrf_field() . method_field('PATCH') . "
                        <button type='submit' class='dropdown-item'>
                            <i class='fa fa-ban mr-2'></i>Desativar
                        </button>
                    </form>
                    <form action='" . route('produtos.destroy', $id) . "' method='POST'
                        onsubmit=\"return confirm('Deseja realmente excluir este produto?')\">
                        " . csrf_field() . method_field('DELETE') . "
                        <button type='submit' class='dropdown-item text-danger'>
                            <i class='fa fa-trash mr-2'></i>Excluir
                        </button>
                    </form>
                </div>
            </div>
        </div>
";

@endphp

<x-app-layout>
    {{-- <x-basic.content-page-fluid>
        <div class="row">
            <div class="col-md-4"> --}}
                <x-basic.content-page :title="__('Produto')" :class="'card-secondary'" :btnAcoes="$btnAcoes" :size="'sm'">
                    <livewire:produto.produto-visualizar :id="$id" :nome="$nome" :estoque_id="$estoque_id"
                        wire:key="produto-visualizar" />
                </x-basic.content-page>
            {{-- </div>
        </div>
    </x-basic.content-page-fluid> --}}
</x-app-layout>
