<div>
    <div class="row align-items-center mb-4">
        <div class="col-md-4 text-center mb-3 mb-md-0">
            <img src="{{ asset('storage/' . $image->imagem) }}" alt="Imagem do Produto" class="img-thumbnail shadow-sm"
                style="max-height: 200px;">
        </div>
        <div class="col-md-4 mb-3 mb-sm-3">
            <h5 class="mb-2"><strong>Nome:</strong> <span class="text-muted">{{ $nome }}</span></h5>
            <p class="mb-0">
                <strong>Última Movimentação:</strong>
                <x-table.status-badge :status="$ultima_movimentacao" />

            </p>
            <p class="mb-0">
                <strong>Preço:</strong>
                {{ \App\Helpers\FormatHelper::brl($produto->preco) }}
            </p>
            <p class="mb-0">
                <strong>Quantidade:</strong>
                {{ $produto->quantidade_produtos }}
            </p>

        </div>
        <div class="col-md-4">
            <button type="button"
                onclick="
                    window.dispatchEvent(new CustomEvent('abrirModal', {
                        detail: {
                            titulo: 'Atualizar produto',
                            formId: 'cadastrarProduto',
                            componente: 'produto.modal-cadastrar-produto'
                        }
                    }));
                    $('#modal-sm').modal('show');
                "
                class="btn btn-outline-warning btn-block btn-sm"><i class="fa fa-book"></i>
                Atualizar Produto {{ $nome }}
            </button>
            <button type="button"
                onclick="
                    window.dispatchEvent(new CustomEvent('abrirModal', {
                        detail: {
                            titulo: 'Atualizar produto',
                            formId: 'cadastrarProduto',
                            componente: 'produto.modal-cadastrar-produto'
                        }
                    }));
                    $('#modal-sm').modal('show');
                "
                class="btn btn-outline-primary btn-block btn-sm"><i class="fa fa-book"></i>
                Adicionar Produto {{ $nome }}
            </button>
            <button type="button"
                onclick="
                    window.dispatchEvent(new CustomEvent('abrirModal', {
                        detail: {
                            titulo: 'Atualizar produto',
                            formId: 'cadastrarProduto',
                            componente: 'produto.modal-cadastrar-produto'
                        }
                    }));
                    $('#modal-sm').modal('show');
                "
                class="btn btn-outline-danger btn-block btn-sm"><i class="fa fa-book"></i>
                Remover Produto {{ $nome }}
            </button>
        </div>
    </div>
</div>
