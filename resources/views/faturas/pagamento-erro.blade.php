<x-guest-layout>
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1>Erro no Pagamento</h1>
                </div>
                <div class="col-sm-6 text-right">
                </div>
            </div>
        </div>
    </section>

    <div class="content text-center mt-5">
        <div class="card shadow p-4 mx-auto" style="max-width: 600px;">
            <div class="mb-3">
                <i class="fa fa-times-circle fa-4x text-danger"></i>
            </div>

            <h2 class="mb-3 text-dark">O pagamento não foi concluído!</h2>

            <p class="text-muted">
                Infelizmente, o pagamento da fatura <strong>{{ $fatura->codigo }}</strong> não foi realizado.
                A assinatura da empresa <strong>{{ $fatura->assinatura->empresa->nome }}</strong> permanece pendente.
            </p>

            <div class="alert alert-danger mt-3">
                <strong>O que você pode fazer:</strong>
                <ul class="mb-0 mt-2">
                    Tente realizar o pagamento novamente mais tarde.
                    Se o problema persistir, entre em contato com o suporte.
                </ul>
            </div>

            <div class="mt-4">
                <a href="{{ $fatura->link_pagamento }}" class="btn btn-danger" target="_blank">
                    <i class="fa fa-credit-card"></i> Tentar novamente
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
