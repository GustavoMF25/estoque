<x-app-layout>
    <x-basic.content-page :title="__('Auditoria')" :class="'card-secondary'">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="h3 mb-1">Auditoria do Sistema</h1>
                    <p class="text-muted mb-0">Eventos registrados no banco de dados em tempo real.</p>
                </div>
                <a href="{{ route('auditoria.index') }}" class="btn btn-outline-secondary">Atualizar</a>
            </div>

            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label">Ação</label>
                    <input type="text" name="action" value="{{ $filters['action'] ?? '' }}" class="form-control" placeholder="ex: produto.created">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Usuário (ID)</label>
                    <input type="number" name="user" value="{{ $filters['user'] ?? '' }}" class="form-control" min="1">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filtrar</button>
                    <a href="{{ route('auditoria.index') }}" class="btn btn-light">Limpar</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Ação</th>
                            <th>Usuário</th>
                            <th>IP</th>
                            <th>User Agent</th>
                            <th>Detalhes</th>
                            <th class="text-nowrap">Registrado em</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr>
                                <td>{{ $log->id }}</td>
                                <td>{{ $log->action }}</td>
                                <td>
                                    @if ($log->user)
                                        {{ $log->user->name }} <span class="text-muted small">(#{{ $log->user_id }})</span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>{{ $log->ip_address ?? '—' }}</td>
                                <td class="small text-muted">{{ \Illuminate\Support\Str::limit($log->user_agent, 40) }}</td>
                                <td><pre class="mb-0 small">{{ json_encode($log->details, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre></td>
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
