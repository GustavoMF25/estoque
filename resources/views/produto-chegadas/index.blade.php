<x-app-layout>
    <x-basic.content-page :title="__('Estoque a Chegar')" :class="'card-secondary'">
        @if (in_array(optional(auth()->user())->perfil, ['admin', 'operador']))
        <form method="POST" action="{{ route('produto-chegadas.store') }}" class="mb-4" id="form-estoque-chegada">
            @csrf
            <div class="row">
                <div class="col-md-4">
                    <label>Produto</label>
                    <select id="produto_id" name="produto_id" class="form-control select2" required>
                        <option value="">Selecione</option>
                        @foreach ($produtos as $produto)
                        <option value="{{ $produto->id }}" @selected(old('produto_id') == $produto->id)>{{ $produto->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Quantidade</label>
                    <input type="number" min="1" name="quantidade_total" class="form-control" value="{{ old('quantidade_total') }}" required>
                </div>
                <div class="col-md-3">
                    <label>Previsão de chegada</label>
                    <input type="date" id="previsao_chegada" name="previsao_chegada" class="form-control"
                        value="{{ old('previsao_chegada') }}">
                </div>
                <div class="col-md-3">
                    <label>Observação</label>
                    <input type="text" name="observacao" class="form-control" value="{{ old('observacao') }}" maxlength="1000">
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Registrar estoque a chegar</button>
            </div>
        </form>
        @endif

        @livewire('produto-chegada-table')
    </x-basic.content-page>
</x-app-layout>

@push('styles')
<link rel="stylesheet" href="{{ asset('adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endpush
