<x-app-layout>
    <x-basic.content-page :title="__('Assinaturas')" :class="'card-secondary'" :btnCadastrar="['route' => route('empresas.create'), 'title' => 'Cadastrar Cliente']">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Empresa</th>
                    <th>Plano</th>
                    <th>Status</th>
                    <th>Valor</th>
                    <th>Expiração</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($empresas as $empresa)
                    @php $assinatura = $empresa->assinatura; @endphp
                    <tr>
                        <td><strong>{{ $empresa->nome }}</strong></td>

                        <td>{{ $assinatura?->plano ?? '—' }}</td>

                        <td>
                            @if ($assinatura)
                                <span
                                    class="badge bg-{{ $assinatura->status === 'ativo' ? 'success' : ($assinatura->status === 'pendente' ? 'warning' : 'danger') }}">
                                    {{ $assinatura->em_teste ? 'Teste' : ucfirst($assinatura->status) }}
                                </span>
                            @else
                                <span class="badge bg-secondary">Sem assinatura</span>
                            @endif
                        </td>

                        <td>
                            {{ $assinatura ? 'R$ ' . number_format($assinatura->valor_mensal, 2, ',', '.') : '—' }}
                        </td>

                        <td>
                            @if ($assinatura)
                                @if ($assinatura->em_teste)
                                    {{ optional($assinatura->trial_expira_em)->format('d/m/Y') ?? '—' }}
                                @else
                                    {{ optional($assinatura->data_vencimento)->format('d/m/Y') ?? '—' }}
                                @endif
                            @else
                                —
                            @endif
                        </td>

                        <td>
                            @if ($assinatura)
                                <a href="{{ route('assinaturas.show', $assinatura->id) }}"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Ver Detalhes
                                </a>
                            @else
                                <a href="{{ route('assinaturas.create', $empresa->id) }}" class="btn btn-sm btn-success">
                                    <i class="bi bi-plus-circle"></i> Criar Assinatura
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-3">
            {{ $empresas->links() }}
        </div>
    </x-basic.content-page>
</x-app-layout>
