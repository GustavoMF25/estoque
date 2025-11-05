<div @refresh-produto-visualizar.window="$wire.loadData()">
    <div class=" align-items-center">
        <div class=" text-center mb-3 mb-md-0">
            <img src="{{ asset('storage/' . $image) }}" alt="Imagem do Produto" class="img-thumbnail shadow-sm"
                style="max-height: 200px;">
        </div>
        <div>
            <div>
                <h5 class="mb-2"><strong>Nome:</strong> <span class="text-muted">{{ $nome }}</span></h5>
                <p class="mb-0">
                    <strong>Última Movimentação:</strong>
                    <x-table.status-badge :status="$ultima_movimentacao" />

                </p>
                <p class="mb-0">
                    <strong>Preço:</strong>
                    {{ \App\Helpers\FormatHelper::brl($produto->preco) }}
                </p>
                {{-- <p class="mb-0">
                <strong>Quantidade:</strong>
                {{ $produto->quantidade_produtos }}
            </p> --}}
                @if (!empty($produto->fabricante_nome))
                    <p class="mb-0">
                        <strong>Fabricante:</strong>
                        {{ $produto->fabricante_nome }}
                    </p>
                @endif
            </div>

            <div class="row mt-2">
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
                            <span class="info-box-number">{{$qtdVendidos}}</span>
                        </div>
                    </div>
                </div>
                @if (auth()->user()->isAdmin())
                    <div class="col-md-4 col-sm-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fa fa-dollar-sign"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">Valor Recebido</span>
                                <span class="info-box-number">{{\App\Helpers\FormatHelper::brl($valorRecebido) }}</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
