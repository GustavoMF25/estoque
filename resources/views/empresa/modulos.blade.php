<x-app-layout>
    <x-basic.content-page-fluid :title="__('Editar Dados da Empresa')" :class="'card-primary'">

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('empresas.modulos.update', $empresas->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-body">
                <div class="row">
                    @foreach ($modulos as $modulo)
                        @php
                            $pivot = $empresas->modulos->firstWhere('id', $modulo->id)?->pivot;
                        @endphp
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="form-check form-switch">
                                            <input type="hidden" name="modulos[{{ $modulo->id }}][status]" value="bloqueado">
                                            <input class="form-check-input" type="checkbox"
                                                id="modulo_{{ $modulo->id }}"
                                                name="modulos[{{ $modulo->id }}][status]"
                                                value="ativo"
                                                {{ optional($pivot)->status === 'ativo' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="modulo_{{ $modulo->id }}">
                                                {{ $modulo->nome }}
                                            </label>
                                        </div>
                                        <span class="badge bg-light text-dark">{{ $modulo->categoria ?? 'Core' }}</span>
                                    </div>
                                    <p class="text-muted small mb-3">{{ $modulo->descricao ?? 'Sem descrição' }}</p>

                                    <div class="mb-2">
                                        <label class="form-label text-xs">Expira em (opcional)</label>
                                        <input type="date" class="form-control form-control-sm"
                                            name="modulos[{{ $modulo->id }}][expira_em]"
                                            value="{{ optional(optional($pivot)->expira_em)->format('Y-m-d') }}">
                                    </div>

                                    @if ($modulo->submodulos->isNotEmpty())
                                        <p class="text-xs text-muted mb-1">Submódulos:</p>
                                        <ul class="list-unstyled text-xs">
                                            @foreach ($modulo->submodulos as $sub)
                                                <li><i class="fa fa-check text-success"></i> {{ $sub->nome }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> Salvar Permissões
                </button>
            </div>
        </form>

    </x-basic.content-page-fluid>
</x-app-layout>
