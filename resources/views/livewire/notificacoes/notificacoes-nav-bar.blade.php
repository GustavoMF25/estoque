<div wire:poll.10s="carregar">
    <a class="nav-link" data-toggle="dropdown" href="#" aria-label="Notificações">
        <i class="fas fa-bell"></i>
        @if ($naoLidas)
            <span class="badge badge-danger navbar-badge">{{ $naoLidas }}</span>
        @endif
    </a>

    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <span class="dropdown-item dropdown-header">{{ $naoLidas }} notificações novas</span>
        <div class="dropdown-divider"></div>

        @forelse ($notificacoes as $notificacao)
            <button type="button" class="dropdown-item text-wrap"
                wire:click="abrir({{ $notificacao->id }})">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="flex-grow-1">
                        <div class="fw-bold">
                            {{ $notificacao->titulo }}
                        </div>
                        <small class="text-muted">{{ $notificacao->mensagem }}</small>
                    </div>
                    <small class="text-muted ml-2">{{ $notificacao->created_at->format('H:i') }}</small>
                </div>
            </button>
            <div class="dropdown-divider"></div>
        @empty
            <div class="dropdown-item text-muted text-center">
                Nenhuma notificação
            </div>
        @endforelse

        <a href="{{ route('notificacoes.index') }}" class="dropdown-item dropdown-footer">
            Ver todas
        </a>
    </div>
</div>
