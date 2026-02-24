<x-app-layout>
    <x-basic.content-page :title="__('Funcionalidades')" :class="'card-secondary'" :btnCadastrarAdmin="['route' => route('funcionalidades.create'), 'title' => 'Cadastrar funcionalidade']">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Slug</th>
                        <th>Módulo</th>
                        <th>Rota</th>
                        <th class="text-center">Menu</th>
                        <th class="text-center">Ativa</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($funcionalidades as $funcionalidade)
                        <tr>
                            <td>{{ $funcionalidade->nome }}</td>
                            <td><code>{{ $funcionalidade->slug }}</code></td>
                            <td>{{ $funcionalidade->modulo ?: '-' }}</td>
                            <td><code>{{ $funcionalidade->rota ?: '-' }}</code></td>
                            <td class="text-center">
                                <span class="badge badge-{{ $funcionalidade->visivel_menu ? 'success' : 'secondary' }}">
                                    {{ $funcionalidade->visivel_menu ? 'Sim' : 'Não' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-{{ $funcionalidade->ativo ? 'success' : 'danger' }}">
                                    {{ $funcionalidade->ativo ? 'Sim' : 'Não' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('funcionalidades.edit', $funcionalidade) }}" class="btn btn-sm btn-primary">
                                    Editar
                                </a>
                                <form action="{{ route('funcionalidades.destroy', $funcionalidade) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Deseja remover esta funcionalidade?')">
                                        Excluir
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Nenhuma funcionalidade cadastrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-basic.content-page>
</x-app-layout>
