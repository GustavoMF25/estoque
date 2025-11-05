<x-app-layout>
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">

                </div>
                <div class="col-sm-6">

                </div>
            </div>
    </section>
    <div class="content text-center mt-5">
        <div class="card shadow p-4 mx-auto" style="max-width: 600px;">
            <div class="mb-3">
                <i class="fa fa-lock fa-4x text-warning"></i>
            </div>

            <h2 class="mb-3 text-dark">🕒 Módulo de Vendas Expirado</h2>
            <p class="text-muted">
            <p>O módulo de <strong>vendas</strong> desta versão foi descontinuado após {{ $dias }} dias.</p>
            </p>

                <p>Conheça a nova versão do sistema com suporte e planos de assinatura!</p>
                <a href="{{ $linkNovaVersao }}" target="_blank" class="btn btn-primary mt-3">
                    Acessar Nova Versão →
                </a>
        </div>
    </div>
</x-app-layout>
