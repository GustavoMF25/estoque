@php
    use App\Helpers\FormatHelper;
@endphp

<x-app-layout>
    <div class="content pt-3">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="mb-1">{{ $titulo ?? 'Dashboard' }}</h4>
                    <small class="text-muted">Perfil: {{ ucfirst($perfil ?? 'usuario') }}</small>
                </div>
                <div class="text-muted small">
                    Atualizado em {{ now()->format('d/m/Y H:i') }}
                </div>
            </div>

            <div class="mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body py-2 px-3 d-flex flex-wrap align-items-center gap-2">
                        <strong class="mr-2">Insígnias do momento:</strong>
                        @forelse (($insignias ?? []) as $insignia)
                            <span class="badge {{ $insignia['classe'] ?? 'badge-secondary' }} px-3 py-2">
                                {{ $insignia['texto'] }}
                            </span>
                        @empty
                            <span class="badge badge-light px-3 py-2">Sem alertas críticos agora</span>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="row">
                @forelse (($cards ?? []) as $card)
                    <div class="col-12 col-md-6 col-xl-4 mb-3">
                        <div class="card dashboard-kpi border-0 h-100 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="text-muted small mb-1">{{ $card['titulo'] }}</div>
                                        <h3 class="mb-0 font-weight-bold">
                                            @if (($card['tipo'] ?? 'numero') === 'moeda')
                                                {{ FormatHelper::brl($card['valor'] ?? 0) }}
                                            @else
                                                {{ number_format((int) ($card['valor'] ?? 0), 0, ',', '.') }}
                                            @endif
                                        </h3>
                                    </div>
                                    <div class="dashboard-icon dashboard-icon-{{ $card['cor'] ?? 'secondary' }}">
                                        <i class="fas {{ $card['icone'] ?? 'fa-chart-bar' }}"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info">Nenhum indicador disponível para este perfil.</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>

@push('styles')
    <style>
        .dashboard-kpi {
            border-radius: 14px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .dashboard-kpi:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 28px rgba(15, 23, 42, 0.14) !important;
        }

        .dashboard-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid transparent;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.18);
        }

        .dashboard-icon i {
            font-size: 1.2rem;
            line-height: 1;
        }

        .dashboard-icon-primary {
            color: #0d6efd;
            background: rgba(13, 110, 253, 0.12);
            border-color: rgba(13, 110, 253, 0.25);
        }

        .dashboard-icon-success {
            color: #198754;
            background: rgba(25, 135, 84, 0.14);
            border-color: rgba(25, 135, 84, 0.28);
        }

        .dashboard-icon-info {
            color: #0dcaf0;
            background: rgba(13, 202, 240, 0.14);
            border-color: rgba(13, 202, 240, 0.28);
        }

        .dashboard-icon-warning {
            color: #f59f00;
            background: rgba(245, 159, 0, 0.16);
            border-color: rgba(245, 159, 0, 0.30);
        }

        .dashboard-icon-danger {
            color: #dc3545;
            background: rgba(220, 53, 69, 0.14);
            border-color: rgba(220, 53, 69, 0.28);
        }

        .dashboard-icon-secondary {
            color: #6c757d;
            background: rgba(108, 117, 125, 0.15);
            border-color: rgba(108, 117, 125, 0.30);
        }

        .dashboard-icon-dark {
            color: #1f2937;
            background: rgba(31, 41, 55, 0.12);
            border-color: rgba(31, 41, 55, 0.24);
        }

        .gap-2 {
            gap: .5rem;
        }
    </style>
@endpush
