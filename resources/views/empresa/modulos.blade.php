<x-app-layout>
    <x-basic.content-page :title="__('Editar Dados da Empresa')" :class="'card-primary'">

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
                            // Verifica se o módulo está associado à empresa
                            $checked = $empresas->modulos->contains($modulo->id);
                        @endphp

                        <div class="col-md-4 mb-3">
                            <div class="form-check border p-3 rounded shadow-sm">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="modulos[]"
                                        value="{{ $modulo->id }}" id="modulo_{{ $modulo->id }}"
                                        {{ $checked ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="modulo_{{ $modulo->id }}">
                                        {{ $modulo->nome }}
                                    </label>
                                </div>
                                <p class="text-muted small mb-0">{{ $modulo->descricao ?? '' }}</p>
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

    </x-basic.content-page>
</x-app-layout>
