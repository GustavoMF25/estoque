<div class="content mx-5">
    <h1>Catálogo de Produtos</h1>
    <div wire:poll.2s.keep-alive>
        <input type="text" wire:model.debounce.500ms="search" placeholder="Buscar produto..."
            class="form-control mb-3" />
        <div wire:loading wire:target="search" class="text-muted mt-2">
            <div class="spinner-border spinner-border-sm text-primary" role="status">
                <span class="visually-hidden"></span>
            </div>
        </div>
    </div>
    <div class="row">
        @foreach ($products as $product)
            <div class="col-md-3">
                <div class="card card-default shadow-sm hover-shadow border border-1">
                    <div class="card-body">
                        <div class="text-center">
                            <img src="{{ $product->imagem ? asset('storage/' . $product->imagem) : '/imagens/no-image.png' }}" alt="Imagem do Produto" class="" style="max-height: 100px;">
                        </div>
                        <p><b>Nome: </b> {{ $product->nome }}</p>
                        <div class="mb-2">
                            <label for="quantidade_{{ $product->id }}" class="form-label">
                                Quantidade:
                            </label>
                            <input type="number" id="quantidade_{{ $product->id }}"
                                name="quantidade_{{ $product->id }}" class="form-control" min="1"
                                max="{{ $product->disponivel_para_venda_count }}"
                                wire:model.defer="quantidades.{{ $product->id }}"
                                placeholder="Máx: {{ $product->disponivel_para_venda_count }}">
                            <small class="text-muted">
                                Disponível: {{ $product->disponivel_para_venda_count }}
                                @if (($product->a_chegar_disponivel_count ?? 0) > 0)
                                    <span class="ml-1 badge badge-warning">A chegar: {{ $product->a_chegar_disponivel_count }}</span>
                                @endif
                            </small>
                        </div>

                        @if (($product->chegadas_abertas_catalogo ?? collect())->isNotEmpty())
                            <div class="mb-3">
                                @php($chegadasAbertas = (bool) ($chegadasAbertasAbertas[$product->id] ?? false))
                                <div class="card card-outline card-warning mb-0">
                                    <div class="card-header p-2">
                                        <button class="btn btn-block text-left text-white font-weight-bold p-0"
                                            type="button"
                                            wire:click="toggleChegadas({{ $product->id }})"
                                            aria-expanded="{{ $chegadasAbertas ? 'true' : 'false' }}">
                                            Próximos recebimentos
                                            <span class="float-right">
                                                <i class="fas fa-chevron-{{ $chegadasAbertas ? 'up' : 'down' }}"></i>
                                            </span>
                                        </button>
                                    </div>
                                    @if ($chegadasAbertas)
                                        <ul class="list-group list-group-flush">
                                            @foreach ($product->chegadas_abertas_catalogo as $chegada)
                                                <li class="list-group-item py-2 px-3">
                                                    <div><b>Quantidade:</b> {{ $chegada['quantidade_disponivel'] }}</div>
                                                    <div>
                                                        <b>Previsão:</b>
                                                        {{ $chegada['previsao_chegada']?->format('d/m/Y') ?? 'Sem previsão' }}
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <p><b>Valor de venda: </b> {{ App\Helpers\FormatHelper::brl($product->valor_venda ?? $product->preco) }}</p>
                        <p></p>

                    </div>
                    <div class="card-footer text-center">
                        <button wire:click="adicionarCarrinho('{{ $product->id }}')" type="button"
                            class="btn btn-primary btn-block">
                            <i class="fas fa-cart-plus"></i>
                            Adicionar </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-4">
        {{ $products->links('livewire::bootstrap') }}
    </div>
</div>
