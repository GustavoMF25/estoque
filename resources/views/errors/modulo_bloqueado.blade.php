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

            <h2 class="mb-3 text-dark">Módulo Bloqueado</h2>
            <p class="text-muted">
                O módulo <strong>{{ $moduloNome ?? 'desconhecido' }}</strong> está disponível apenas em planos pagos.
            </p>

            <div class="alert alert-warning mt-3">
                <strong>Upgrade necessário:</strong>
                Para desbloquear este módulo, adquira um plano compatível ou entre em contato com o administrador.
            </div>
        </div>
    </div>
</x-app-layout>
