<x-app-layout>
    <x-basic.content-page :title="__('Auditoria')" :class="'card-secondary'">
        <div class="container-fluid">
            <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="h3 mb-1">Auditoria do Sistema</h1>
                    <p class="text-muted mb-0">Eventos registrados no banco de dados em tempo real.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="text-muted small">
                        @if ($logs->total())
                            Exibindo {{ $logs->firstItem() }}-{{ $logs->lastItem() }} de {{ $logs->total() }}
                        @else
                            Nenhum evento encontrado
                        @endif
                    </div>
                    <a href="{{ route('auditoria.index') }}" class="btn btn-outline-secondary btn-sm">Atualizar</a>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label">Ação</label>
                            <input type="text" name="action" value="{{ $filters['action'] ?? '' }}" class="form-control" placeholder="ex: produto.created">
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label">Usuário (ID)</label>
                            <input type="number" name="user" value="{{ $filters['user'] ?? '' }}" class="form-control" min="1">
                        </div>
                        <div class="col-lg-5 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                            <a href="{{ route('auditoria.index') }}" class="btn btn-light">Limpar</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-responsive shadow-sm rounded">
                <table class="table table-bordered table-hover table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Ação</th>
                            <th>Usuário</th>
                            <th>IP</th>
                            <th>Navegador</th>
                            <th>Detalhes</th>
                            <th class="text-nowrap">Registrado em</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr>
                                <td>{{ $log->id }}</td>
                                <td><span class="badge badge-info">{{ $log->action }}</span></td>
                                <td>
                                    @if ($log->user)
                                        {{ $log->user->name }} <span class="text-muted small">(#{{ $log->user_id }})</span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>{{ $log->ip_address ?? '—' }}</td>
                                <td class="small text-muted text-truncate" style="max-width: 200px;" title="{{ $log->user_agent }}">
                                    {{ \Illuminate\Support\Str::limit($log->user_agent, 60) }}
                                </td>
                                <td style="min-width: 220px;">
                                    @php
                                        $details = $log->details ? json_encode($log->details, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : null;
                                    @endphp
                                    @if ($details)
                                        <details>
                                            <summary class="small text-primary">Ver detalhes</summary>
                                            <pre class="mb-0 small mt-2">{{ $details }}</pre>
                                        </details>
                                    @else
                                        <span class="text-muted small">Sem detalhes</span>
                                    @endif
                                </td>
                                <td class="text-nowrap">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Nenhum evento encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $logs->links() }}
            </div>
        </div>
    </x-basic.content-page>
</x-app-layout>
