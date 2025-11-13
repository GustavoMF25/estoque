<div class="content mx-5 py-5">
    <div class="row">
        {{-- Lista de itens do carrinho --}}
        <div class="col-md-8">
            @forelse($itens as $item)
                <div class="card card-primary card-outline shadow-sm mb-3 border border-1 hover-shadow">
                    <div class="card-header">
                        <strong>{{ $item['nome'] }}</strong>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <img src="{{ asset('storage/' . $item['imagem']) }}" class="rounded mr-3"
                                style="width: 60px; height: 60px; object-fit: cover;">
                            <div>
                                <strong>{{ $item['nome'] }}</strong>
                                <div class="small text-muted">
                                    {{ $disponiveis[$item['produto_id']] ?? 0 }} disponíveis
                                </div>
                                <div>
                                    <a href="#" wire:click.prevent="removerItem('{{ $item['produto_id'] }}')" class="text-danger small">Excluir</a>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center">
                            <button type="button"
                                wire:click="diminuirQuantidade('{{ $item['produto_id'] }}', '{{ $item['nome'] }}')"
                                class="btn btn-sm btn-light border">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="text" readonly class="form-control form-control-sm mx-1 text-center"
                                style="width: 50px;" value="{{ $item['quantidade'] }}">
                            <button type="button"
                                wire:click="aumentarQuantidade('{{ $item['produto_id'] }}', '{{ $item['nome'] }}')"
                                class="btn btn-sm btn-light border">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>

                        <div class="ml-3 text-right">
                            <strong> {{ App\Helpers\FormatHelper::brl($item['preco_unitario'] * $item['quantidade']) }}</strong>
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-warning text-center">Carrinho vazio 🛒</div>
            @endforelse
        </div>


        {{-- Informações e confirmação da venda --}}
        <div class="col-md-4">
            <div class="card card-primary card-outline shadow-sm border border-1 hover-shadow">
                <div class="card-header">
                    Informações da Venda
                </div>
                <form wire:submit.prevent="confirmar">
                    <div class="card-body">
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

                        @if ($enderecoSelecionado)
                            <div class="alert alert-info mt-2">
                                <strong>Endereço:</strong> {{ $enderecoSelecionado }}
                            </div>
                        @endif

                        {{-- Valor da venda --}}
                        <div class="form-group mt-3">
                            <label>Valor da Venda (editar se necessário)</label>
                            <input type="number" step="0.10" wire:model.live="total_original" class="form-control"
                                placeholder="Valor final da venda">
                        </div>

                        {{-- Desconto em % --}}
                        <div class="form-group mt-3">
                            <label>Desconto (%)</label>
                            <input type="number" step="0.01" wire:model.live="desconto_percentual"
                                class="form-control" placeholder="0%">
                        </div>

                        {{-- Total original --}}
                        <div class="mt-3">
                            <small class="text-muted">
                                Total original: {{ App\Helpers\FormatHelper::brl($total_original ?? 0) }}
                            </small>
                        </div>

                        {{-- Total final --}}
                        <div class="mt-2">
                            <strong>Total Final:</strong>
                            <span class="float-right">
                                {{ App\Helpers\FormatHelper::brl($total_final ?? 0) }}
                            </span>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <button type="submit" class="btn btn-primary">Confirmar Venda</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
