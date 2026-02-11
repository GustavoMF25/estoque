<div>
    <div class="row mb-3">
        <div class="col-md-6">
            <div><strong>Venda:</strong> #{{ $venda->id }}</div>
            <div><strong>Protocolo:</strong> {{ $venda->protocolo }}</div>
            <div><strong>Status:</strong> {{ ucfirst($venda->status) }}</div>
        </div>
        <div class="col-md-6">
            <div><strong>Vendedor:</strong> {{ $venda->usuario->name ?? 'N/A' }}</div>
            <div><strong>Loja:</strong> {{ $venda->loja->nome ?? 'N/A' }}</div>
            <div><strong>Data:</strong> {{ $venda->created_at->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <div><strong>Cliente:</strong> {{ $venda->cliente->nome ?? 'N/A' }}</div>
            <div><strong>Documento:</strong> {{ $venda->cliente->documento ?? 'N/A' }}</div>
            <div><strong>Telefone:</strong> {{ $venda->cliente->telefone ?? 'N/A' }}</div>
        </div>
        <div class="col-md-6">
            <div><strong>Valor total:</strong> {{ \App\Helpers\FormatHelper::brl($venda->valor_total) }}</div>
            <div><strong>Desconto:</strong> {{ \App\Helpers\FormatHelper::brl($venda->desconto ?? 0) }}</div>
            <div><strong>Valor final:</strong> {{ \App\Helpers\FormatHelper::brl($venda->valor_final ?? $venda->valor_total) }}</div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Quantidade</th>
                    <th>Valor unitario</th>
                    <th>Valor total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($venda->itens as $item)
                    <tr>
                        <td>{{ $item->produto->nome ?? 'N/A' }}</td>
                        <td>{{ $item->quantidade }}</td>
                        <td>{{ \App\Helpers\FormatHelper::brl($item->valor_unitario) }}</td>
                        <td>{{ \App\Helpers\FormatHelper::brl($item->valor_total) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
