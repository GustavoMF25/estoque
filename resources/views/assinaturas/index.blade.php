<x-app-layout>
    <x-basic.content-page :title="__('Assinaturas')" :class="'card-secondary'" :btnCadastrar="['route' => route('assinaturas.create'), 'title' => 'Cadastrar Assinante']">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>Empresa</th>
                    <th>Plano</th>
                    <th>Status</th>
                    <th>Início</th>
                    <th>Vencimento</th>
                    <th>Valor (R$)</th>
                    <th>Método</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assinaturas as $a)
                    <tr>
                        <td>{{ $a->empresa->nome ?? '—' }}</td>
                        <td>{{ ucfirst($a->plano) }}</td>
                        <td>
                            @switch($a->status)
                                @case('ativo')
                                    <span class="badge bg-success">Ativo</span>
                                @break

                                @case('pendente')
                                    <span class="badge bg-warning text-dark">Pendente</span>
                                @break

                                @case('atrasado')
                                    <span class="badge bg-danger">Atrasado</span>
                                @break

                                @case('cancelado')
                                    <span class="badge bg-secondary">Cancelado</span>
                                @break
                            @endswitch
                        </td>
                        <td>{{ \Carbon\Carbon::parse($a->data_inicio)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($a->data_vencimento)->format('d/m/Y') }}</td>
                        <td>{{ number_format($a->valor_mensal, 2, ',', '.') }}</td>
                        <td>{{ strtoupper($a->metodo_pagamento) }}</td>
                        <td>
                            <a href="{{ route('assinaturas.edit', $a->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-pen"></i>
                            </a>
                            <form action="{{ route('assinaturas.destroy', $a->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Tem certeza que deseja remover esta assinatura?')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                            <form action="{{ route('assinaturas.renovar', $a->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success"
                                    onclick="return confirm('Deseja renovar esta assinatura por mais 30 dias?')">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">Nenhuma assinatura cadastrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $assinaturas->links() }}
            </div>
        </x-basic.content-page>
    </x-app-layout>
