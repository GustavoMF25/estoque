<div class="content mx-3 mx-md-4 py-4 venda-page">
    <div class="d-flex align-items-center mb-3 text-muted small">
        <span>Dashboard</span>
        <span class="mx-2">/</span>
        <span>Vendas</span>
        <span class="mx-2">/</span>
        <span class="text-dark">Nova Venda</span>
    </div>

    <h2 class="venda-title mb-4">
        <i class="fas fa-shopping-cart mr-2 text-muted"></i>
        Confirmar Venda
    </h2>

    <form wire:submit.prevent="confirmar">
        <div class="row">
            <div class="col-lg-4 mb-3">
                <div class="card venda-card">
                    <div class="card-header venda-card-header">Produtos</div>
                    <div class="card-body p-0">
                        @forelse($itens as $item)
                            <div class="produto-row">
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('storage/' . $item['imagem']) }}" class="produto-thumb mr-3" alt="{{ $item['nome'] }}">
                                    <div>
                                        <div class="font-weight-bold">{{ $item['nome'] }}</div>
                                        <div class="text-muted small">Estoque disponível: {{ $disponiveis[$item['produto_id']] ?? 0 }}</div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div class="d-flex align-items-center">
                                        <button type="button" wire:click="diminuirQuantidade('{{ $item['produto_id'] }}', '{{ $item['nome'] }}')"
                                            class="btn btn-light border btn-sm px-2">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="text" readonly class="form-control form-control-sm text-center mx-1 qtd-input"
                                            value="{{ $item['quantidade'] }}">
                                        <button type="button" wire:click="aumentarQuantidade('{{ $item['produto_id'] }}', '{{ $item['nome'] }}')"
                                            class="btn btn-light border btn-sm px-2">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <div class="item-price-box">
                                        <div class="text-muted small mb-1">Valor unitário</div>
                                        <div class="input-group input-group-sm mb-2 unit-price-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">R$</span>
                                            </div>
                                            <input type="number" step="0.01" min="0"
                                                wire:model.live="preco_unitario.{{ $item['produto_id'] }}"
                                                class="form-control text-right">
                                        </div>
                                        <label class="price-update-check mb-1">
                                            <input type="checkbox"
                                                wire:model.live="atualizar_preco_base.{{ $item['produto_id'] }}">
                                            <span>Atualizar preço na base</span>
                                        </label>
                                        <div class="produto-total">{{ App\Helpers\FormatHelper::brl($item['preco_unitario'] * $item['quantidade']) }}</div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-3">
                                <div class="alert alert-warning text-center mb-0">Carrinho vazio 🛒</div>
                            </div>
                        @endforelse
                    </div>
                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong>Subtotal</strong>
                            <strong class="h4 mb-0">{{ App\Helpers\FormatHelper::brl($total) }}</strong>
                        </div>
                    </div>
                </div>
                <a href="{{ route('produtos.catalogo') }}" class="d-inline-block mt-3 text-primary">
                    <i class="fas fa-arrow-left mr-1"></i> Voltar para produtos
                </a>
            </div>

            <div class="col-lg-5 mb-3">
                <div class="card venda-card h-100">
                    <div class="card-header venda-card-header">Dados da Venda</div>
                    <div class="card-body">
                        <div class="venda-block mb-3">
                            <div class="venda-block-title">Informações Gerais</div>
                            <div class="form-group">
                                <label for="protocolo">Protocolo (Número do Pedido)</label>
                                <input type="text" wire:model.defer="protocolo" id="protocolo" class="form-control"
                                    placeholder="Informe o protocolo do pedido">
                                @error('protocolo')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="cliente_id">Cliente</label>
                                <select wire:model.change="cliente_id" id="cliente_id" class="form-control">
                                    <option value="">-- Selecione o Cliente --</option>
                                    @foreach ($clientes as $cliente)
                                        <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                                    @endforeach
                                </select>
                                @error('cliente_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            @if ($usuarioTemLojaVinculada)
                                <div class="form-group">
                                    <label>Loja vinculada ao vendedor</label>
                                    <input type="text" class="form-control" value="{{ $nomeLojaVinculada }}" readonly>
                                </div>
                            @else
                                <div class="form-group mb-0">
                                    <label for="loja_id">Loja para nota</label>
                                    <select wire:model.live="loja_id" id="loja_id" class="form-control">
                                        <option value="">-- Selecione a Loja --</option>
                                        @foreach ($lojas as $loja)
                                            <option value="{{ $loja->id }}">{{ $loja->nome }}</option>
                                        @endforeach
                                    </select>
                                    @error('loja_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif

                            @if ($enderecoSelecionado)
                                <div class="alert alert-info mt-3 mb-0">
                                    <strong>Endereço:</strong> {{ $enderecoSelecionado }}
                                </div>
                            @endif
                        </div>

                        <div class="venda-block mb-3">
                            <div class="venda-block-title">Pagamento</div>
                            <div class="form-group">
                                <label for="frete">Valor do frete</label>
                                <input type="number" step="0.01" wire:model.live="frete" id="frete" class="form-control"
                                    placeholder="0,00">
                            </div>

                            <div class="form-group">
                                <label for="forma_pagamento">Método de pagamento</label>
                                <select wire:model.live="forma_pagamento" id="forma_pagamento" class="form-control">
                                    <option value="">-- Informar depois --</option>
                                    <option value="dinheiro">Dinheiro</option>
                                    <option value="pix">Pix</option>
                                    <option value="cartao_credito">Cartão de crédito</option>
                                    <option value="cartao_debito">Cartão de débito</option>
                                    <option value="boleto">Boleto</option>
                                    <option value="transferencia">Transferência</option>
                                    <option value="outro">Outro</option>
                                </select>
                                @error('forma_pagamento')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="status_pagamento">Situação do pagamento</label>
                                <select wire:model.live="status_pagamento" id="status_pagamento" class="form-control">
                                    <option value="">-- Não informado --</option>
                                    <option value="pendente">Pendente</option>
                                    <option value="parcial">Parcial</option>
                                    <option value="pago">Pago</option>
                                    <option value="cancelado">Cancelado</option>
                                </select>
                                @error('status_pagamento')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            @if ($forma_pagamento === 'cartao_credito')
                                <div class="form-group mb-0">
                                    <label for="parcelas_cartao">Quantidade de parcelas</label>
                                    <input type="number" min="1" max="24" step="1" wire:model.live="parcelas_cartao"
                                        id="parcelas_cartao" class="form-control" placeholder="Ex: 3">
                                    @error('parcelas_cartao')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        </div>

                        <div class="venda-block">
                            <div class="venda-block-title">Ajustes</div>
                            <div class="form-group">
                                <label for="valor_venda">Valor da Venda (editar se necessário)</label>
                                <input type="number" step="0.01" wire:model.live="valor_venda" id="valor_venda"
                                    class="form-control" placeholder="0,00">
                            </div>

                            <div class="form-group mb-0">
                                <label for="desconto_percentual">Desconto (%)</label>
                                <input type="number" step="0.01" min="0" max="100" wire:model.live="desconto_percentual"
                                    id="desconto_percentual" class="form-control" placeholder="0%">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <button type="submit" class="btn btn-primary btn-lg btn-block">
                            <i class="fas fa-shopping-cart mr-2"></i> Finalizar Venda
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 mb-3">
                <div class="card venda-card">
                    <div class="card-header venda-card-header">Resumo da Venda</div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between resumo-line">
                            <span>Subtotal</span>
                            <strong>{{ App\Helpers\FormatHelper::brl($total) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between resumo-line">
                            <span>Frete</span>
                            <strong>{{ App\Helpers\FormatHelper::brl($financeiro['frete']) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between resumo-line">
                            <span>Desconto</span>
                            <strong>- {{ App\Helpers\FormatHelper::brl($financeiro['desconto_total']) }}</strong>
                        </div>
                        <div class="resumo-total mt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>TOTAL</span>
                                <strong>{{ App\Helpers\FormatHelper::brl($financeiro['total_final']) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('styles')
    <style>
        .venda-page .venda-title {
            font-weight: 700;
            color: #212529;
        }

        .venda-page .venda-card {
            border: 1px solid #d9dee3;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        }

        .venda-page .venda-card-header {
            background: #f7f8fa;
            border-bottom: 1px solid #e3e7eb;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            font-size: 1.05rem;
            font-weight: 700;
            color: #2e3b4e;
        }

        .venda-page .produto-row {
            padding: 14px 16px;
            border-bottom: 1px solid #eef1f4;
        }

        .venda-page .produto-thumb {
            width: 58px;
            height: 58px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #e6eaee;
        }

        .venda-page .qtd-input {
            width: 52px;
        }

        .venda-page .produto-total {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1;
            color: #1f2d3d;
            text-align: right;
        }

        .venda-page .item-price-box {
            width: 180px;
        }

        .venda-page .unit-price-group .input-group-text {
            background: #f3f6f9;
            color: #4a5968;
            border-color: #dbe3ea;
            font-weight: 600;
        }

        .venda-page .unit-price-group .form-control {
            border-color: #dbe3ea;
            font-weight: 600;
        }

        .venda-page .price-update-check {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: .82rem;
            color: #3a4a5a;
            cursor: pointer;
            user-select: none;
        }

        .venda-page .price-update-check input {
            margin: 0;
        }

        .venda-page .resumo-line {
            padding: 8px 0;
            border-bottom: 1px solid #edf0f3;
            color: #27384a;
        }

        .venda-page .resumo-total {
            background: #e9f4ed;
            border: 1px solid #cde3d4;
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 1.4rem;
            color: #2f6650;
        }

        .venda-page .venda-block {
            border: 1px solid #e8edf2;
            border-radius: 8px;
            padding: 12px;
            background: #fbfcfd;
        }

        .venda-page .venda-block-title {
            font-size: .95rem;
            font-weight: 700;
            color: #2e3b4e;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #e6ebf0;
        }

        @media (max-width: 991px) {
            .venda-page .produto-total {
                font-size: 1.3rem;
            }
        }
    </style>
@endpush
