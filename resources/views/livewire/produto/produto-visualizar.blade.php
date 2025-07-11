<div>
    <div class="row align-items-center mb-4">
        <div class="col-md-3 text-center mb-3 mb-md-0">
            <img src="{{ asset('storage/' . $image->imagem) }}" alt="Imagem do Produto" class="img-thumbnail shadow-sm"
                style="max-height: 200px;">
        </div>
        <div class="col-md-9">
            <h5 class="mb-2"><strong>Nome:</strong> <span class="text-muted">{{ $nome }}</span></h5>
            <p class="mb-0">
                <strong>Última Movimentação:</strong>
                <x-table.status-badge :status="$ultima_movimentacao" />

            </p>
        </div>
    </div>

    <livewire:produto.produtos-visualizar-table :nome="$nome" :ultima_movimentacao="$ultima_movimentacao" :wire:key="'produtos-visualizar'" />
</div>
