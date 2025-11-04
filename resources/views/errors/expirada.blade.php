<x-app-layout>
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    {{-- espaço reservado caso queira breadcrumb futuramente --}}
                </div>
                <div class="col-sm-6 text-right">
                    {{-- espaço reservado --}}
                </div>
            </div>
        </div>
    </section>

    <div class="content text-center mt-5">
        <div class="card shadow p-4 mx-auto border-warning" style="max-width: 600px;">

            {{-- Ícone principal --}}
            <div class="mb-3">
                <i class="fa fa-lock fa-4x text-warning"></i>
            </div>

            {{-- Título --}}
            <h2 class="mb-2 text-dark">Assinatura Expirada</h2>

            {{-- Mensagem principal --}}
            <p class="text-muted mb-4">
                A assinatura da empresa <strong>{{ $empresaNome ?? auth()->user()->empresa->nome ?? 'sua empresa' }}</strong> expirou em
                <strong>{{ isset($dataExpiracao) ? \Carbon\Carbon::parse($dataExpiracao)->format('d/m/Y') : 'data não informada' }}</strong>.
            </p>

            {{-- Alerta de renovação --}}
            <div class="alert alert-warning text-start">
                <strong>Renovação necessária:</strong><br>
                Para continuar utilizando o sistema e acessar todos os módulos, renove sua assinatura agora mesmo.
            </div>

            {{-- Botões de ação --}}
            <div class="mt-3">
                <a href="{{ route('logout') }}" class="btn btn-outline-secondary ms-2"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fa fa-sign-out-alt"></i> Sair
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
