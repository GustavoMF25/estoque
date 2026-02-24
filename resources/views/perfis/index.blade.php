<x-app-layout>
    <x-basic.content-page :title="__('Perfis')" :class="'card-secondary'" :btnCadastrarAdmin="['route' => route('perfis.create'), 'title' => 'Cadastrar perfil']">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Slug</th>
                        <th>Descrição</th>
                        <th class="text-center">Ativo</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($perfis as $perfil)
                        <tr>
                            <td>{{ $perfil->nome }}</td>
                            <td><code>{{ $perfil->slug }}</code></td>
                            <td>{{ $perfil->descricao ?: '-' }}</td>
                            <td class="text-center">
                                <span class="badge badge-{{ $perfil->ativo ? 'success' : 'secondary' }}">
                                    {{ $perfil->ativo ? 'Sim' : 'Não' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('perfis.edit', $perfil) }}" class="btn btn-sm btn-primary">Editar</a>
                                <form action="{{ route('perfis.destroy', $perfil) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Deseja remover este perfil?')">
                                        Excluir
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Nenhum perfil cadastrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-basic.content-page>
</x-app-layout>
