<x-app-layout>
    <x-basic.content-page :title="__($assinatura->empresa->nome)" :class="'card-secondary'" :btnCadastrar="['route' =>route('faturas.create', $assinatura->id), 'title' => 'Nova Fatura']">
        <div class="container-fluid">

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="row">
                <div class="col-md-6">
                    <p><strong>Plano:</strong> {{ $assinatura->plano }}</p>
                    <p><strong>Valor:</strong> {{ App\Helpers\FormatHelper::brl($assinatura->valor_mensal) }}</p>
                    <p><strong>Status:</strong>
                        <span
                            class="badge bg-{{ $assinatura->status === 'ativa' ? 'success' : ($assinatura->status === 'pendente' ? 'warning' : 'danger') }}">
                            {{ ucfirst($assinatura->status) }}
                        </span>
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>Data de Início:</strong>
                        {{ \Carbon\Carbon::parse($assinatura->data_inicio)->format('d/m/Y') }}</p>
                    <p><strong>Expira em:</strong>
                        {{ \Carbon\Carbon::parse($assinatura->data_vencimento)->format('d/m/Y') }}</p>
                    <p><strong>Última atualização:</strong> {{ $assinatura->updated_at->format('d/m/Y H:i') }}
                    </p>
                </div>
            </div>

            <livewire:super.fatura-table :assinatura-id="$assinatura->id" />

        </div>
    </x-basic.content-page>
</x-app-layout>
