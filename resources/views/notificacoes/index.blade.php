<x-app-layout>
    <x-basic.content-page :title="__('Notificações')" :class="'card-secondary'">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Título</th>
                        <th>Mensagem</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($notificacoes as $notificacao)
                        <tr>
                            <td>{{ $notificacao->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $notificacao->titulo }}</td>
                            <td>{{ $notificacao->mensagem }}</td>
                            <td>
                                @if ($notificacao->lida_em)
                                    <span class="badge badge-secondary">Lida</span>
                                @else
                                    <span class="badge badge-warning">Nova</span>
                                @endif
                            </td>
                            <td>
                                @if (!$notificacao->lida_em)
                                    <form method="POST" action="{{ route('notificacoes.ler', $notificacao->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-success" type="submit">Marcar como lida</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">Nenhuma notificação encontrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $notificacoes->links('livewire::bootstrap') }}
        </div>
    </x-basic.content-page>
</x-app-layout>
