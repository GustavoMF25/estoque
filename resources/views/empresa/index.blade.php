<x-app-layout>
    <x-basic.content-page :title="__('Empresas')" :class="'card-secondary'" :btnCadastrar="['route' => route('empresas.create'), 'title' => 'Cadastrar Empresas']">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>Logo</th>
                    <th>Nome</th>
                    <th>CNPJ</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($empresas as $empresa)
                    <tr>
                        <td>
                            @if ($empresa->logo)
                                <img src="{{ asset('storage/' . $empresa->logo) }}" alt="Logo" width="50"
                                    class="rounded">
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $empresa->nome }}</td>
                        <td>{{ $empresa->cnpj }}</td>
                        <td>{{ $empresa->email ?? '—' }}</td>
                        <td>{{ $empresa->telefone ?? '—' }}</td>
                        <td>
                            <a href="{{ route('empresas.edit', $empresa->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-pen"></i>
                            </a>
                            <form action="{{ route('empresas.destroy', $empresa->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Tem certeza que deseja excluir esta empresa?')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">Nenhuma empresa cadastrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-3">
            {{ $empresas->links() }}
        </div>
        </div>
    </x-basic.content-page>
</x-app-layout>
