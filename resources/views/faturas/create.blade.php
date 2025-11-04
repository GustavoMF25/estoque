<x-app-layout>
    <x-basic.content-page :title="__('Fatura')" :class="'card-secondary'">
        <form action="{{ route('faturas.store', $assinatura->id) }}" method="POST">
            @csrf

            {{-- Empresa vinculada --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Empresa</label>
                <input type="text" class="form-control" value="{{ $assinatura->empresa->nome }}" readonly>
            </div>

            {{-- Plano vinculado --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Plano</label>
                <input type="text" class="form-control" value="{{ $assinatura->plano }}" readonly>
            </div>

            {{-- Valor da fatura --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Valor (R$)</label>
                <input type="number" name="valor" class="form-control" step="0.01" value="{{$assinatura->valor_mensal}}" placeholder="Ex: 59.90"
                    required>
            </div>

            {{-- Data de vencimento --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Data de Vencimento</label>
                <input type="date" name="data_vencimento" class="form-control" required>
            </div>

            {{-- Método de pagamento (opcional) --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Método de Pagamento</label>
                <select name="metodo_pagamento" class="form-select">
                    <option value="">Selecione (opcional)</option>
                    <option value="pix">PIX</option>
                    <option value="boleto">Boleto</option>
                    <option value="cartao">Cartão de Crédito</option>
                    <option value="manual">Manual</option>
                </select>
            </div>

            {{-- Observações --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Observações</label>
                <textarea name="observacoes" class="form-control" rows="3"
                    placeholder="Ex: Fatura referente à renovação de novembro."></textarea>
            </div>

            {{-- Botão salvar --}}
            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Salvar Fatura
                </button>
            </div>
        </form>
    </x-basic.content-page>
</x-app-layout>
