<x-app-layout>
    <div class="content pt-3">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Estoque</h4>
                @if (optional(auth()->user())->isAdmin())
                    <a href="{{ route('estoques.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i> Cadastrar Estoque
                    </a>
                @endif
            </div>

            <div class="row mb-4">
            @forelse ($cards as $card)
                @php
                    $ocupacao = $card['ocupacao_percentual'];
                    $progressClass = 'bg-success';
                    if (!is_null($ocupacao) && $ocupacao >= 80) {
                        $progressClass = 'bg-danger';
                    } elseif (!is_null($ocupacao) && $ocupacao >= 60) {
                        $progressClass = 'bg-warning';
                    }
                @endphp

                <div class="col-12 col-md-6 col-xl-4 mb-3">
                    <div class="card estoque-card card-outline {{ $card['status'] === 'ativo' ? 'card-success' : 'card-secondary' }} h-100 border-0">
                        <div class="card-header estoque-card-header">
                            <div class="row w-100 align-items-start">
                                <div class="col-7 col-md-8">
                                    <div class="estoque-card-title-wrap">
                                        <h3 class="card-title mb-0">{{ $card['nome'] }}</h3>
                                        <span class="badge estoque-status-badge {{ $card['status'] === 'ativo' ? 'badge-success' : 'badge-secondary' }}">
                                            {{ ucfirst($card['status']) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-5 col-md-4">
                                    <div class="estoque-card-actions-wrap text-right">
                                        <div class="dropdown">
                                            <button class="btn btn-xs btn-light border estoque-btn-menu" type="button"
                                                id="acoesEstoque{{ $card['id'] }}" data-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right shadow-sm"
                                                aria-labelledby="acoesEstoque{{ $card['id'] }}">
                                                <button type="button" class="dropdown-item"
                                                    onclick="window.dispatchEvent(new CustomEvent('abrirModal', { detail: { titulo: 'Estoque → {{ addslashes($card['nome']) }}', componente: 'estoque.estoque-visualizar', props: { estoqueId: {{ $card['id'] }}, size: 'modal-md' } } })); $('#modal-sm').modal('show');">
                                                    <i class="fas fa-eye mr-2 text-primary"></i> Ver detalhes
                                                </button>

                                                @if (optional(auth()->user())->isAdmin())
                                                    <div class="dropdown-divider"></div>
                                                    <form method="POST" action="{{ route('estoques.destroy', $card['id']) }}"
                                                        onsubmit="return confirm('Deseja realmente remover este estoque?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="fas fa-trash-alt mr-2"></i> Excluir
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="small text-muted mb-2">Loja: {{ $card['loja'] ?? 'N/A' }}</div>
                            <div class="row text-center">
                                <div class="col-4 border-right">
                                    <div class="h5 mb-0">{{ $card['produtos_ativos'] }}</div>
                                    <small class="text-muted">Produtos</small>
                                </div>
                                <div class="col-4 border-right">
                                    <div class="h5 mb-0">{{ $card['unidades_disponiveis'] }}</div>
                                    <small class="text-muted">Disponíveis</small>
                                </div>
                                <div class="col-4">
                                    <div class="h5 mb-0">{{ $card['unidades_vendidas'] }}</div>
                                    <small class="text-muted">Vendidas</small>
                                </div>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span>Total físico:</span>
                                <strong>{{ $card['unidades_totais'] }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>A chegar disponível:</span>
                                <strong>{{ $card['a_chegar_disponivel'] }}</strong>
                            </div>

                            @if (!is_null($ocupacao))
                                <div class="mt-3">
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span>Ocupação</span>
                                        <span>{{ $ocupacao }}% de {{ $card['limite'] }}</span>
                                    </div>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar {{ $progressClass }}" role="progressbar"
                                            style="width: {{ min($ocupacao, 100) }}%"></div>
                                    </div>
                                </div>
                            @else
                                <div class="small text-muted mt-3">Sem limite máximo configurado.</div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info mb-3">Nenhum estoque cadastrado até o momento.</div>
                </div>
            @endforelse
            </div>
        </div>
    </div>
</x-app-layout>

@push('styles')
    <style>
        .estoque-card {
            border-radius: 14px;
            box-shadow: 0 10px 28px rgba(16, 24, 40, 0.10);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .estoque-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 38px rgba(16, 24, 40, 0.18);
        }

        .estoque-card-header {
            padding-right: .75rem;
            padding-left: .75rem;
        }

        .estoque-card-title-wrap {
            min-width: 0;
            flex: 1 1 auto;
        }

        .estoque-card-title-wrap .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            max-width: 100%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .estoque-card-actions-wrap {
            display: flex;
            flex-direction: row;
            align-items: flex-end;
            justify-content: flex-end;
            gap: .45rem;
            flex: 0 0 auto;
            width: 100%;
        }

        .estoque-status-badge {
            font-size: .78rem;
            padding: .35rem .55rem;
        }

        .estoque-btn-menu {
            min-width: 32px;
            padding: .15rem .45rem;
        }

        @media (max-width: 576px) {
            .estoque-card-actions-wrap {
                align-items: center;
            }
        }
    </style>
@endpush
