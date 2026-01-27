<div class="container">
    @php
        $itensCount = $venda->itens->count();
        $linhasExtras = max(0, 15 - $itensCount);
        $logoFile = $empresa->logo ?? null;
        $logoPath = $logoFile ? public_path('storage/' . $logoFile) : public_path('storage/logos/logo-fake.png');
        $logoSrc = null;
        if ($logoPath && file_exists($logoPath)) {
            $logoExt = pathinfo($logoPath, PATHINFO_EXTENSION) ?: 'png';
            $logoData = base64_encode(file_get_contents($logoPath));
            $logoSrc = 'data:image/' . $logoExt . ';base64,' . $logoData;
        }
    @endphp
    {{-- Cabeçalho --}}
    <div class="header">
        <table style="width: 100%; border-bottom: 1px solid #000; margin-bottom: 5px;">
            <tr>
                <td style="width: 20%; text-align: left;">
                    @if (!empty($logoSrc))
                        <img src="{{ $logoSrc }}" style="height: 80px;">
                    @endif
                </td>
                <td style="width: 80%; text-align: right; font-size: 12px;">
                    <strong style="font-size: 14px;">{{ strtoupper($empresa->nome ?? 'EMPRESA NÃO INFORMADA') }}</strong><br>
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
                    <th>Descrição do Produto</th>
                    <th class="center">Qtde</th>
                    <th class="right">Vlr. Unitário</th>
                    <th class="right">Vlr. Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($venda->itens as $item)
                    <tr>
                        <td>{{ $item->produto->nome ?? '-' }}</td>
                        <td class="center">{{ $item->quantidade }}</td>
                        <td class="right">R$ {{ number_format($item->valor_unitario, 2, ',', '.') }}</td>
                        <td class="right">R$ {{ number_format($item->valor_total, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
                @for ($i = 0; $i < $linhasExtras; $i++)
                    <tr>
                        <td>&nbsp;</td>
                        <td class="center"></td>
                        <td class="right"></td>
                        <td class="right"></td>
                    </tr>
                @endfor
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
                <td class="right">R$ {{ number_format($venda->desconto ?? 0, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Valor Líquido:</strong></td>
                <td class="right total">R$
                    {{ number_format($venda->valor_total - ($venda->desconto ?? 0), 2, ',', '.') }}</td>
            </tr>
        </table>
    </div>

 

    {{-- Pagamento --}}
    <div class="secao">
        <div class="secao-titulo">INFORMAÇÕES DE PAGAMENTO</div>
        <table>
            <tr>
                <td><strong>Forma de Pagamento:</strong> {{ ucfirst($venda->forma_pagamento ?? '') }}</td>
                <td><strong>Situação:</strong> {{ ucfirst($venda->status_pagamento ?? '') }}</td>
            </tr>
        </table>
    </div>

    {{-- Observações --}}
    <div class="secao">
        <div class="secao-titulo">OBSERVAÇÕES</div>
        <table>
            <tr>
                <td style="height: 80px;"></td>
            </tr>
        </table>
    </div>

    {{-- Assinatura --}}
    <div class="assinatura">
        <div>Assinatura do Cliente</div>
    </div>

    {{-- Rodapé --}}
    <div class="footer">
        Emitido por {{ $empresa->nome ?? 'Sistema de Estoque' }} em {{ now()->format('d/m/Y H:i') }}
    </div>
</div>
