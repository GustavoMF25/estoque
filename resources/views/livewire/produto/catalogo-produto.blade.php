<div class="container">
    <h1>Catálogo de Produtos</h1>
    <div wire:poll.2s.keep-alive>
        <input
            type="text"
            wire:model.debounce.500ms="search"
            placeholder="Buscar produto..."
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
            <div class="card card-default shadow-sm hover-shadow border border-success border-1">
                <div class="card-body">
                    <div class="text-center">
                        <img src="{{ asset('storage/' . $product->imagem) }}" alt="Imagem do Produto" class=""
                            style="max-height: 100px;">
                    </div>
                    <p><b>Nome: </b> {{ $product->nome }}</p>
                    <div class="mb-2">
                        <label for="quantidade_{{ $product->id }}" class="form-label">
                            Quantidade:
                        </label>
                        <input
                            type="number"
                            id="quantidade_{{ $product->id }}"
                            name="quantidade_{{ $product->id }}"
                            class="form-control"
                            min="1"
                            max="{{ $product->quantidade_produtos }}"
                            wire:model.defer="quantidades.{{ $product->id }}"
                            placeholder="Máx: {{ $product->quantidade_produtos }}">
                        <small class="text-muted">
                            Disponível: {{ $product->quantidade_produtos }}
                        </small>
                    </div>

                    <p><b>Preço: </b> {{ App\Helpers\FormatHelper::brl($product->preco)  }}</p>
                    <p></p>

                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4">
        {{ $products->links('livewire::bootstrap') }}
    </div>
</div>