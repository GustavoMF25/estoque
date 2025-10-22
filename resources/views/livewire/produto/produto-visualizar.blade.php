<div @refresh-produto-visualizar.window="$wire.loadData()">
    <div class=" align-items-center">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="fw-bold mb-1 text-dark">{{ $produto->nome }}</h4>
                <p class="mb-1 text-muted"><strong>Preço:</strong> <span class="text-dark">R$
                        {{ number_format($produto->preco, 2, ',', '.') }}</span></p>
                <p class="mb-3 text-muted"><strong>Estoque:</strong> {{ $produto->estoque->nome ?? 'N/A' }}</p>

                <div class="d-flex row gap-4">
                    <div class="col-md-4 col-sm-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="fa fa-warehouse"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">Em estoque</span>
                                <span class="info-box-number">{{ $qtdDisponiveis }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fa fa-store"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">Vendidos</span>
                                <span class="info-box-number">{{ $qtdVendidos }}</span>
                            </div>
                        </div>
                    </div>
                    @if (auth()->user()->isAdmin())
                        <div class="col-md-4 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fa fa-dollar-sign"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">Valor Recebido</span>
                                    <span
                                        class="info-box-number">{{ \App\Helpers\FormatHelper::brl($valorRecebido) }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class4="col-md-4 text-center">
                <img src="{{  $image ? asset('storage/' . $image) : '/imagens/no-image.png' }}" alt="Imagem do Produto"
                    class="img-fluid rounded shadow-sm border" style="max-height: 180px; object-fit: contain;">
            </div>
        </div>

    </div>
</div>
