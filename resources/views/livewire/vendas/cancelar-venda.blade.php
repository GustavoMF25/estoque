<div>
    <div class="alert alert-warning mb-3">
        <strong>Atencao:</strong> esta acao vai cancelar a venda e devolver os itens para disponiveis.
    </div>

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
                    <th>Valor unitario</th>
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
        <label>Motivo do cancelamento (opcional)</label>
        <textarea class="form-control" rows="2" wire:model.defer="motivo"></textarea>
        @error('motivo') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-danger" wire:click="cancelar">Cancelar venda</button>
    </div>
</div>
