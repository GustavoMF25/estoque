<x-app-layout>
    <x-basic.content-page :title="__('Modelos de Nota')" :class="'card-secondary'"
        :btnCadastrarAdmin="['route' => route('nota-modelos.create'), 'title' => 'Cadastrar Modelo']">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Ícone</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($modelos as $modelo)
                        <tr>
                            <td>{{ $modelo->nome }}</td>
                            <td>
                                @if ($modelo->icone)
                                    <i class="{{ $modelo->icone }}"></i>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $modelo->ativo ? 'Ativo' : 'Inativo' }}</td>
                            <td>
                                <a href="{{ route('nota-modelos.edit', $modelo->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <form action="{{ route('nota-modelos.destroy', $modelo->id) }}" method="POST"
                                    onsubmit="return confirm('Deseja remover este modelo?')" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">Nenhum modelo cadastrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $modelos->links('livewire::bootstrap') }}
        </div>
    </x-basic.content-page>
</x-app-layout>
