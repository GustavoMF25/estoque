<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>DAFEN - Documento Auxiliar de Fatura Eletrônica Nacional</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #000;
            margin: 15px;
        }

        .container {
            border: 1px solid #000;
            padding: 10px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            padding-bottom: 8px;
            margin-bottom: 5px;
        }

        .div-logo {
            width: 100%;
            text-align: center;
        }

        .logo {
            width: 20%;
        }

        .logo img {
            height: 70px;
        }

        .empresa {
            width: 100%;
            text-align: center;
            font-size: 12px;
        }

        .empresa strong {
            font-size: 14px;
        }

        .documento {
            text-align: center;
            border: 1px solid #000;
            padding: 4px;
            background: #f2f2f2;
            font-weight: bold;
        }

        .secao {
            border: 1px solid #000;
            margin-top: 5px;
        }

        .secao-titulo {
            background: #f2f2f2;
            font-weight: bold;
            padding: 2px 5px;
            border-bottom: 1px solid #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            padding: 3px 5px;
            border: 1px solid #000;
        }

        th {
            background: #f8f8f8;
            text-align: left;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .no-border td {
            border: none;
        }

        .total {
            font-size: 12px;
            font-weight: bold;
        }

        .assinatura {
            margin-top: 40px;
            text-align: center;
        }

        .assinatura div {
            border-top: 1px solid #000;
            width: 220px;
            margin: 0 auto;
            padding-top: 5px;
            font-size: 10px;
        }

        .footer {
            font-size: 9px;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">

        {{-- Cabeçalho --}}
        <div class="header">
            <table style="width: 100%; border-bottom: 1px solid #000; margin-bottom: 5px;">
                <tr>
                    <td style="width: 20%; text-align: left;">
                        @if (isset($empresa->logo) && file_exists(public_path('storage/' . $empresa->logo)))
                            <img src="{{ public_path('storage/' . $empresa->logo) }}" style="height: 80px;">
                        @endif
                    </td>
                    <td style="width: 80%; text-align: right; font-size: 12px;">
                        <strong
                            style="font-size: 14px;">{{ strtoupper($empresa->nome ?? 'EMPRESA NÃO INFORMADA') }}</strong><br>
                        CNPJ: {{ $empresa->cnpj ?? '-' }}<br>
                        {{ $empresa->endereco ?? '-' }}<br>
                        Tel: {{ $empresa->telefone ?? '-' }}
                    </td>
                </tr>
            </table>
        </div>

        <div class="documento">
            <small>Protocolo: {{ $venda->protocolo }} | Data: {{ $venda->created_at->format('d/m/Y H:i') }}</small>
        </div>

        {{-- Destinatário --}}
        <div class="secao">
            <div class="secao-titulo">DESTINATÁRIO</div>
            <table>
                <tr>
                    <td><strong>Nome/Razão Social:</strong> {{ $venda->cliente->nome ?? '-' }}</td>
                    <td><strong>CPF/CNPJ:</strong> {{ $venda->cliente->documento ?? '-' }}</td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Endereço:</strong>
                        {{ optional(optional($venda->cliente)->enderecoPadrao)->rua ?? '' }},
                        {{ optional(optional($venda->cliente)->enderecoPadrao)->numero ?? '' }}
                        {{ optional(optional($venda->cliente)->enderecoPadrao)->bairro ? '- ' . optional(optional($venda->cliente)->enderecoPadrao)->bairro : '' }},
                        {{ optional(optional($venda->cliente)->enderecoPadrao)->cidade ?? '' }}/{{ optional(optional($venda->cliente)->enderecoPadrao)->estado ?? '' }}
                        {{ optional(optional($venda->cliente)->enderecoPadrao)->cep ? '- CEP ' . optional(optional($venda->cliente)->enderecoPadrao)->cep : '' }}
                    </td>
                </tr>

                <tr>
                    <td><strong>Telefone:</strong> {{ $venda->cliente->telefone ?? '-' }}</td>
                    <td><strong>Email:</strong> {{ $venda->cliente->email ?? '-' }}</td>
                </tr>
            </table>
        </div>

        {{-- Identificação da Venda --}}
        <div class="secao">
            <div class="secao-titulo">IDENTIFICAÇÃO DA VENDA</div>
            <table>
                <tr>
                    <td><strong>Usuário:</strong> {{ $venda->usuario->name ?? '-' }}</td>
                    <td><strong>Loja:</strong> {{ $venda->loja->nome ?? '-' }}</td>
                    <td><strong>Status:</strong> {{ ucfirst($venda->status) }}</td>
                </tr>
            </table>
        </div>

        {{-- Produtos --}}
        <div class="secao">
            <div class="secao-titulo">ITENS DA VENDA</div>
            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Descrição do Produto</th>
                        <th class="center">Qtde</th>
                        <th class="right">Vlr. Unitário</th>
                        <th class="right">Vlr. Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($venda->itens as $item)
                        <tr>
                            <td>{{ $item->produto->id ?? '-' }}</td>
                            <td>{{ $item->produto->nome ?? '-' }}</td>
                            <td class="center">{{ $item->quantidade }}</td>
                            <td class="right">R$ {{ number_format($item->valor_unitario, 2, ',', '.') }}</td>
                            <td class="right">R$ {{ number_format($item->valor_total, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Totais --}}
        <div class="secao">
            <div class="secao-titulo">TOTAIS</div>
            <table>
                <tr>
                    <td><strong>Valor Bruto:</strong></td>
                    <td class="right">R$ {{ number_format($venda->valor_total, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td><strong>Descontos:</strong></td>
                    <td class="right"> {{$venda->desconto ?? 0}} %</td>
                </tr>
                <tr>
                    <td><strong>Valor Líquido:</strong></td>
                    <td class="right total">R$
                        {{ number_format($venda->valor_final, 2, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        {{-- Transporte --}}
        <div class="secao">
            <div class="secao-titulo">TRANSPORTE</div>
            <table>
                <tr>
                    <td><strong>Modalidade do Frete:</strong> {{ $venda->frete_modalidade ?? 'Sem frete' }}</td>
                    <td><strong>Transportadora:</strong> {{ $venda->transportadora ?? '-' }}</td>
                </tr>
            </table>
        </div>

        {{-- Pagamento --}}
        <div class="secao">
            <div class="secao-titulo">INFORMAÇÕES DE PAGAMENTO</div>
            <table>
                <tr>
                    <td><strong>Forma de Pagamento:</strong> {{ ucfirst($venda->forma_pagamento ?? 'Não informada') }}
                    </td>
                    <td><strong>Situação:</strong> {{ ucfirst($venda->status_pagamento ?? 'Pendente') }}</td>
                </tr>
            </table>
        </div>

        {{-- Assinatura --}}
        <div class="assinatura">
            <div>Assinatura do Cliente</div>
        </div>

        {{-- Rodapé --}}
        <div class="footer">
            Emitido por {{ $empresa->nome ?? 'Sistema de Estoque' }} em {{ now()->format('d/m/Y H:i') }}</p>
        </div>

    </div>
</body>

</html>
