<div>
    <div class="mb-3">
        <div><strong>Venda:</strong> #{{ $venda->id }}</div>
        <div><strong>Vendedor:</strong> {{ $venda->usuario->name ?? 'N/A' }}</div>
        <div><strong>Protocolo:</strong> {{ $venda->protocolo }}</div>
    </div>

    <div class="table-responsive mb-3">
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Quantidade</th>
                    <th>Valor unitário</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($venda->itens as $item)
                    <tr>
                        <td>{{ $item->produto->nome ?? 'N/A' }}</td>
                        <td>{{ $item->quantidade }}</td>
                        <td>{{ \App\Helpers\FormatHelper::brl($item->valor_unitario) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="form-group">
        <label>Motivo da recusa (obrigatório para recusar)</label>
        <textarea class="form-control" rows="2" wire:model.defer="motivo"></textarea>
        @error('motivo') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-success" wire:click="aprovar">Aprovar</button>
        <button type="button" class="btn btn-danger" wire:click="recusar">Recusar</button>
    </div>
</div>
