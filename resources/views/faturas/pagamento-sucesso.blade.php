<x-app-layout>
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Pagamento Concluído</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('assinaturas.show', $fatura->assinatura_id) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-arrow-left"></i> Voltar à assinatura
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="content text-center mt-5">
        <div class="card shadow p-4 mx-auto" style="max-width: 600px;">
            <div class="mb-3">
                <i class="fa fa-check-circle fa-4x text-success"></i>
            </div>

            <h2 class="mb-3 text-dark">Pagamento realizado com sucesso!</h2>

            <p class="text-muted">
                O pagamento da fatura <strong>{{ $fatura->codigo }}</strong> foi confirmado.
                A assinatura da empresa <strong>{{ $fatura->assinatura->empresa->nome }}</strong> foi renovada com sucesso.
            </p>

            <div class="alert alert-success mt-3">
                <strong>Detalhes:</strong>
                <ul class="mb-0 mt-2">
                    <li><strong>Data de pagamento:</strong> {{ \Carbon\Carbon::parse($fatura->data_pagamento)->format('d/m/Y') }}</li>
                    <li><strong>Valor pago:</strong> R$ {{ number_format($fatura->valor, 2, ',', '.') }}</li>
                </ul>
            </div>

            <div class="mt-4">
                <a href="{{ route('dashboard') }}" class="btn btn-primary">
                    <i class="fa fa-home"></i> Voltar ao Dashboard
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
