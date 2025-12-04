<div @refresh-produto-visualizar.window="$wire.loadData()">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="row align-items-center">
                {{-- COLUNA IMAGEM / TAGS --}}
                <div class="col-md-4 text-center mb-3 mb-md-0">
                    <img src="{{ $image ? asset('storage/' . $image) : '/imagens/no-image.png' }}" alt="Imagem do Produto"
                        class="img-fluid rounded shadow-sm border" style="max-height: 200px; object-fit: contain;">

                    <div class="mt-3">
                        @if ($produto->categoria)
                            <span class="badge bg-secondary me-1">
                                {{ $produto->categoria->nome }}
                            </span>
                        @endif

                        @if ($produto->fabricante)
                            <span class="badge bg-light text-dark border">
                                {{ $produto->fabricante->nome }}
                            </span>
                        @endif
                    </div>

                    <p class="mt-2 mb-0 text-muted small">
                        Código de barras:
                        <strong>{{ $produto->codigo_barras ?? 'N/A' }}</strong>
                    </p>
                </div>

                {{-- COLUNA INFORMAÇÕES --}}
                <div class="col-md-8">
                    {{-- Cabeçalho nome + preço --}}
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h4 class="fw-bold mb-1 text-dark">{{ $produto->nome }}</h4>
                            <span class="text-muted small">
                                Estoque:
                                <strong>{{ $produto->estoque->nome ?? 'N/A' }}</strong>
                            </span>
                        </div>
                        <div class="text-end">
                            <span class="d-block text-muted small">Preço</span>
                            <span class="display-6 text-success fw-bold" style="font-size: 1.7rem;">
                                R$ {{ number_format($produto->preco, 2, ',', '.') }}
                            </span>
                            <span class="badge mt-1 {{ $produto->ativo ? 'bg-success' : 'bg-danger' }}">
                                {{ $produto->ativo ? 'Ativo' : 'Inativo' }}
                            </span>
                        </div>
                    </div>

                    {{-- Grade de informações rápidas --}}
                    <div class="row mb-3">
                        <div class="col-sm-6 mb-2">
                            <small class="text-muted d-block">Unidade</small>
                            <span class="fw-semibold">
                                {{ $produto->unidade ?? '-' }}
                            </span>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <small class="text-muted d-block">Estoque mínimo</small>
                            <span class="fw-semibold">
                                {{ $produto->estoque_minimo ?? '-' }}
                            </span>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <small class="text-muted d-block">Categoria</small>
                            <span class="fw-semibold">
                                {{ optional($produto->categoria)->nome ?? '-' }}
                            </span>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <small class="text-muted d-block">Fabricante</small>
                            <span class="fw-semibold">
                                {{ optional($produto->fabricante)->nome ?? '-' }}
                            </span>
                        </div>
                    </div>

                    {{-- Indicadores principais --}}
                    <div class="row g-3">
                        <div class="col-md-4 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="fa fa-warehouse"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Em estoque</span>
                                    <span class="info-box-number">{{ $qtdDisponiveis }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-success">
                                    <i class="fa fa-store"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Vendidos</span>
                                    <span class="info-box-number">{{ $qtdVendidos }}</span>
                                </div>
                            </div>
                        </div>

                        @if (auth()->user()->isAdmin())
                            <div class="col-md-4 col-sm-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success">
                                        <i class="fa fa-dollar-sign"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Valor recebido</span>
                                        <span class="info-box-number">
                                            {{ \App\Helpers\FormatHelper::brl($valorRecebido) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12 mb-2">
                    <h5 class="mb-2">Movimentações do produto</h5>
                    <livewire:produto-movimentacoes-table :produtoId="$id" />
                </div>
                <hr />
                <div class="col-md-12">
                    <h5 class="mb-2">Unidades do produto</h5>
                    <livewire:produto.produto-unidades-table :produtoId="$id" />
                </div>
            </div>

        </div>
    </div>
</div>
