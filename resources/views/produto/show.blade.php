<div>
    <div class="mb-3">
        <h5 class="mb-1"><strong>Informações do Produto</strong></h5>

        <p><strong>Nome:</strong> {{ $produto->nome }}</p>
        <p><strong>Código de Barras:</strong> {{ $produto->codigo_barras }}</p>
        <p><strong>Unidade:</strong> {{ $produto->unidade }}</p>
        <p><strong>Preço:</strong>{{ \App\Helpers\FormatHelper::brl($produto->preco) }}</p>
        <p><strong>Estoque:</strong> {{ $produto->estoque->nome ?? '—' }}</p>
        <p><strong>Status:</strong>
            <span class="badge badge-{{ $produto->ativo ? 'success' : 'secondary' }}">
                {{ $produto->ativo ? 'Ativo' : 'Inativo' }}
            </span>
        </p>
        <p><strong>Criado em:</strong> {{ $produto->created_at->format('d/m/Y H:i') }}</p>

        @if ($produto->imagem)
        <div class="text-center mt-3">
            <img src="{{ asset('storage/' . $produto->imagem) }}" class="img-thumbnail" style="max-width: 150px;"
                alt="Imagem do Produto">
        </div>
        @endif
    </div>

    <hr>


    <div id="accordion" class="mt-5">
        <div class="card card-primary">
            <div class="card-header">
                <h4 class="card-title w-100 text-center">
                    <a class="d-block w-100" data-toggle="collapse" href="#collapseOne">
                        <h5><strong>Movimentações</strong> <i class="fas fa-arrow-down"></i></h5>
                    </a>
                </h4>
            </div>
            <div id="collapseOne" class="collapse" data-parent="#accordion">
                <div class="card-body">
                    @if ($produto->movimentacoes->isEmpty())
                    <p class="text-muted">Nenhuma movimentação registrada.</p>
                    @else
                    <livewire:produto-movimentacoes-table :produto-id="$produto->id" />
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>