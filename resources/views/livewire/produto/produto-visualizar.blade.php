<div @refresh-produto-visualizar.window="$wire.loadData()">
    
            <div class="row g-4 align-items-center">
                <div class="col-md-3 text-center">
                    <div class="border rounded-3 p-3 bg-light">
                        <img src="{{ $image ? asset('storage/' . $image) : '/imagens/no-image.png' }}"
                            alt="Imagem do Produto"
                            class="img-fluid rounded"
                            style="max-height: 220px; object-fit: contain;">
                    </div>
                </div>

                <div class="col-md-9">
                    <h4 class="fw-bold mb-2 text-dark">{{ $produto->nome }}</h4>
                    <div class="d-flex flex-column gap-1 mb-4 text-muted small">
                        <div><strong>Estoque:</strong> {{ $produto->estoque->nome ?? 'N/A' }}</div>
                        <div><strong>Fabricante:</strong> {{ $produto->fabricante->nome ?? 'N/A' }}</div>
                        <div><strong>Categoria:</strong> {{ $produto->categoria->nome ?? 'N/A' }}</div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 h-100 bg-white">
                                <div class="text-muted small">Valor de venda</div>
                                <div class="fw-bold text-success fs-5">
                                    {{ \App\Helpers\FormatHelper::brl($produto->valor_venda ?? $produto->preco) }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 h-100 bg-white">
                                <div class="text-muted small">Valor de entrada</div>
                                <div class="fw-bold fs-5">
                                    {{ \App\Helpers\FormatHelper::brl($produto->valor_entrada ?? 0) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4 col-sm-6">
                            <div class="border rounded-3 p-3 h-100 d-flex align-items-center gap-3">
                                <div class="rounded-5 bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width: 44px; height: 44px;">
                                    <i class="fa fa-warehouse"></i>
                                </div>
                                <div class="ml-2">
                                    <div class="text-muted small">Em estoque</div>
                                    <div class="fw-bold fs-5">{{ $qtdDisponiveis }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="border rounded-3 p-3 h-100 d-flex align-items-center gap-3">
                                <div class="rounded-5 bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width: 44px; height: 44px;">
                                    <i class="fa fa-store"></i>
                                </div>
                                <div class="ml-2">
                                    <div class="text-muted small">Vendidos</div>
                                    <div class="fw-bold fs-5">{{ $qtdVendidos }}</div>
                                </div>
                            </div>
                        </div>
                        @if (auth()->user()->isAdmin())
                            <div class="col-md-4 col-sm-6">
                                <div class="border rounded-3 p-3 h-100 d-flex align-items-center gap-3">
                                    <div class="rounded-5 bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="width: 44px; height: 44px;">
                                        <i class="fa fa-dollar-sign"></i>
                                    </div>
                                    <div class="ml-2">
                                        <div class="text-muted small">Valor recebido</div>
                                        <div class="fw-bold fs-5">{{ \App\Helpers\FormatHelper::brl($valorRecebido) }}</div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        
</div>
