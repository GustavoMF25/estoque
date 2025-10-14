<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Nota de Venda - {{ $venda->protocolo }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header, .footer { text-align: center; }
        .logo { margin-bottom: 10px; }
        .title { font-size: 18px; font-weight: bold; margin-bottom: 5px; }
        .subtitle { font-size: 14px; margin-bottom: 15px; }
        .info, .total { width: 100%; margin-bottom: 15px; }
        .info td { padding: 5px; vertical-align: top; }
        .items-table { width: 100%; border-collapse: collapse; }
        .items-table th, .items-table td { border: 1px solid #ddd; padding: 6px; }
        .items-table th { background-color: #f8f8f8; text-align: left; }
        .right { text-align: right; }
        .signature { margin-top: 40px; text-align: center; font-size: 11px; }
        .signature div { border-top: 1px solid #333; width: 200px; margin: 0 auto; padding-top: 5px; }
    </style>
</head>

<body>

    {{-- Logo da Empresa --}}
    @if (isset($empresa->logo) && file_exists(public_path('storage/' . $empresa->logo)))
        <div class="logo">
            <img src="{{ public_path('storage/' . $empresa->logo) }}" alt="Logo da Empresa" style="height: 60px;">
        </div>
    @endif

    {{-- Título --}}
    <div class="header">
        <div class="title">Nota de Venda</div>
        <div class="subtitle">
            Protocolo: {{ $venda->protocolo }} |
            Data: {{ $venda->created_at->format('d/m/Y H:i') }}
        </div>
    </div>

    {{-- Dados da Empresa --}}
    <table class="info">
        <tr>
            <td><strong>Empresa:</strong> {{ $empresa->nome ?? '-' }}</td>
            <td><strong>CNPJ:</strong> {{ $empresa->cnpj ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Telefone:</strong> {{ $empresa->telefone ?? '-' }}</td>
            <td><strong>Endereço:</strong> {{ $empresa->endereco ?? '-' }}</td>
        </tr>
    </table>

    {{-- Dados do Cliente --}}
    @if ($venda->cliente)
        <table class="info">
            <tr>
                <td><strong>Cliente:</strong> {{ $venda->cliente->nome }}</td>
                <td><strong>Documento:</strong> {{ $venda->cliente->documento ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Email:</strong> {{ $venda->cliente->email ?? '-' }}</td>
                <td><strong>Telefone:</strong> {{ $venda->cliente->telefone ?? '-' }}</td>
            </tr>
            @if($venda->cliente->enderecoPadrao)
                <tr>
                    <td colspan="2">
                        <strong>Endereço:</strong>
                        {{ $venda->cliente->enderecoPadrao->rua ?? '' }},
                        {{ $venda->cliente->enderecoPadrao->numero ?? '' }}
                        {{ $venda->cliente->enderecoPadrao->bairro ? '- '.$venda->cliente->enderecoPadrao->bairro : '' }},
                        {{ $venda->cliente->enderecoPadrao->cidade ?? '' }}/{{ $venda->cliente->enderecoPadrao->estado ?? '' }}
                        {{ $venda->cliente->enderecoPadrao->cep ? '- CEP '.$venda->cliente->enderecoPadrao->cep : '' }}
                    </td>
                </tr>
            @endif
        </table>
    @endif

    {{-- Dados da Venda --}}
    <table class="info">
        <tr>
            <td><strong>Usuário:</strong> {{ $venda->usuario->name ?? '-' }}</td>
            <td><strong>Loja:</strong> {{ $venda->loja->nome ?? '-' }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Status:</strong> {{ ucfirst($venda->status) }}</td>
        </tr>
    </table>

    {{-- Itens da Venda --}}
    <table class="items-table">
        <thead>
            <tr>
                <th>Produto</th>
                <th>Preço Unitário</th>
                <th>Quantidade</th>
                <th class="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($venda->itens as $item)
                <tr>
                    <td>{{ $item->produto->nome ?? '-' }}</td>
                    <td>R$ {{ number_format($item->valor_unitario, 2, ',', '.') }}</td>
                    <td>1</td>
                    <td class="right">R$ {{ number_format($item->valor_total, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Total --}}
    <table class="total">
        <tr>
            <td class="right">
                <strong>Total da Venda:</strong>
                R$ {{ number_format($venda->valor_total, 2, ',', '.') }}
            </td>
        </tr>
    </table>

    {{-- Rodapé --}}
    <div class="footer">
        <p>Obrigado pela sua compra!</p>
    </div>

    {{-- Assinatura --}}
    <div class="signature">
        <div>Assinatura do Cliente</div>
    </div>

</body>
</html>
