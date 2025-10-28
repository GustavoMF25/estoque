{{-- <x-app-layout>
<section class="content-header">
        <div class="container-fluid">
            
        </div><!-- /.container-fluid -->
    </section>
    <div class="content mt-5">
        <div class="error-page h-full text-center">
            <h2 class="headline text-warning">403</h2>

            <div class="error-content mt-4">
                <h3><i class="fas fa-exclamation-triangle text-warning"></i> Acesso Negado</h3>

                <p>
                    Você não tem permissão para acessar esta página.<br>
                    Se você acha que isso é um erro, entre em contato com o administrador do sistema.
                </p>

                <div class="mt-4">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                    <a href="{{ url('/') }}" class="btn btn-primary ml-2">
                        <i class="fas fa-home"></i> Ir para a página inicial
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> --}}
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

            <h2 class="mb-3 text-dark">Acesso Negado</h2>
            <p class="text-muted">
                Você não tem permissão para acessar esta página.<br>
                Se você acha que isso é um erro, entre em contato com o administrador do sistema.
            </p>
        </div>
    </div>
</x-app-layout>
