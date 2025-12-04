<x-app-layout>
    <x-basic.content-page-fluid>
        <div class="container-fluid">

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="info-box bg-primary text-white">
                        <span class="info-box-icon"><i class="fas fa-layer-group"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Plano</span>
                            <span class="info-box-number">{{ $assinatura->plano }}</span>
                            <small>{{ ucfirst($assinatura->status) }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="info-box bg-success text-white">
                        <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Valor mensal</span>
                            <span
                                class="info-box-number">{{ App\Helpers\FormatHelper::brl($assinatura->valor_mensal) }}</span>
                            <small>Anual: {{ App\Helpers\FormatHelper::brl($valorAnual) }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="info-box bg-warning">
                        <span class="info-box-icon"><i class="fas fa-hourglass-half"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Dias restantes</span>
                            <span class="info-box-number">
                                @if ($assinatura->periodicidade === 'vitalicio')
                                    Vitalício
                                @else
                                    {{ $diasRestantes !== null ? $diasRestantes : '—' }}
                                @endif
                            </span>
                            @if ($progressoCiclo !== null && $assinatura->periodicidade !== 'vitalicio')
                                <div class="progress">
                                    <div class="progress-bar" style="width: {{ $progressoCiclo }}%"></div>
                                </div>
                                <small>{{ $progressoCiclo }}% do ciclo utilizado</small>
                            @else
                                <small>Sem data de expiração definida</small>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="info-box bg-danger text-white">
                        <span class="info-box-icon"><i class="fas fa-file-invoice-dollar"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Faturas pendentes</span>
                            <span class="info-box-number">{{ $faturasPendentes }}</span>
                            <small>Atualizado {{ $assinatura->updated_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-7">
                    <div class="card card-outline card-primary">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="col-md-6 d-flex justify-content-start align-items-center">
                                <h3 class="card-title mb-0">Detalhes da assinatura</h3>

                            </div>
                            <div class="col-md-6 d-flex justify-content-end align-items-center">
                                <div class="btn-group btn-group-sm flex">
                                    @if ($podeRenovar)
                                        <a href="{{ route('assinaturas.edit', $assinatura->id) }}"
                                            class="btn btn-warning">
                                            <i class="fas fa-edit"></i> Ajustar periodicidade
                                        </a>
                                        <a href="{{ route('assinaturas.renovar', $assinatura->id) }}"
                                            class="btn btn-success"
                                            onclick="return confirm('Confirmar renovação por mais 30 dias?')">
                                            <i class="fas fa-sync-alt"></i> Renovar +30 dias
                                        </a>
                                        <a href="{{ route('faturas.create', $assinatura->id) }}"
                                            class="btn btn-primary">
                                            <i class="fas fa-file-invoice"></i> Nova fatura
                                        </a>
                                    @else
                                        <a href="https://wa.me/5521974332531?text=Preciso%20renovar%20minha%20assinatura%20{{ urlencode($assinatura->empresa->nome) }}"
                                            class="btn btn-outline-primary" target="_blank">
                                            <i class="fas fa-headset"></i> Solicitar suporte
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Plano:</strong> {{ $assinatura->plano }}</p>
                                    <p><strong>Periodicidade:</strong>
                                        {{ ucfirst($assinatura->periodicidade ?? 'mensal') }}</p>
                                    <p><strong>Status:</strong>
                                        <span
                                            class="badge badge-{{ $assinatura->status === 'ativo' ? 'success' : ($assinatura->status === 'pendente' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($assinatura->status) }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Iniciada em:</strong>
                                        {{ optional($assinatura->data_inicio)->format('d/m/Y') }}</p>
                                    <p><strong>Vence em:</strong>
                                        @if ($assinatura->periodicidade === 'vitalicio')
                                            Não expira
                                        @else
                                            {{ optional($assinatura->data_vencimento)->format('d/m/Y') }}
                                        @endif
                                    </p>
                                    <p><strong>Última atualização:</strong>
                                        {{ $assinatura->updated_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card card-outline card-secondary">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="col-6 d-flex justify-content-start align-items-center">
                                <h3 class="card-title mb-0">Últimas faturas</h3>
                            </div>
                            <div class="col-6 d-flex justify-content-end">
                                @if ($podeRenovar)
                                    <a href="{{ route('faturas.index', $assinatura->id) }}"
                                        class="btn btn-sm btn-link">Ver
                                        todas</a>
                                @endif
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                @forelse ($ultimasFaturas as $fatura)
                                    <li class="list-group-item d-flex justify-content-between">
                                        <div>
                                            <strong>{{ $fatura->codigo ?? 'Fatura #' . $fatura->id }}</strong>
                                            <small class="d-block text-muted">
                                                Vence {{ optional($fatura->data_vencimento)->format('d/m/Y') }} • Valor
                                                {{ App\Helpers\FormatHelper::brl($fatura->valor) }}
                                            </small>
                                        </div>
                                        <span
                                            class="badge badge-{{ $fatura->status === 'pago' ? 'success' : ($fatura->status === 'pendente' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($fatura->status) }}
                                        </span>
                                    </li>
                                @empty
                                    <li class="list-group-item text-muted">Nenhuma fatura encontrada.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-info">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="col-6 d-flex justify-content-start">
                                <h3 class="card-title mb-0">Módulos habilitados</h3>

                            </div>
                            <div class="col-6 d-flex justify-content-end">
                                @if ($podeEditarModulos && auth()->user()->isSuperAdmin())
                                    <a href="{{ route('empresas.modulos.edit', $assinatura->empresa_id) }}"
                                        class="btn btn-sm btn-primary">
                                        <i class="fas fa-cogs"></i> Configurar módulos
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-wrap">
                                @forelse ($assinatura->empresa->modulos as $modulo)
                                    <span
                                        class="badge badge-{{ $modulo->pivot->ativo ? 'success' : 'secondary' }} mr-2 mb-2">
                                        {{ $modulo->nome }} @if (!$modulo->pivot->ativo)
                                            (inativo)
                                        @endif
                                    </span>
                                @empty
                                    <span class="text-muted">Nenhum módulo configurado.</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <livewire:super.fatura-table :assinatura-id="$assinatura->id" />

        </div>
    </x-basic.content-page-fluid>
</x-app-layout>
