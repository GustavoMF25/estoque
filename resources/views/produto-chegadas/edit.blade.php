<x-app-layout>
    <x-basic.content-page :title="__('Editar Estoque a Chegar')" :class="'card-secondary'" :back="route('produto-chegadas.index')">
        <form method="POST" action="{{ route('produto-chegadas.update', $produtoChegada) }}" id="form-estoque-chegada-editar">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-4">
                    <label for="produto_id">Produto</label>
                    <select id="produto_id" name="produto_id" class="form-control select2 @error('produto_id') is-invalid @enderror" required>
                        <option value="">Selecione</option>
                        @foreach ($produtos as $produto)
                            <option value="{{ $produto->id }}" @selected(old('produto_id', $produtoChegada->produto_id) == $produto->id)>
                                {{ $produto->nome }}
                            </option>
                        @endforeach
                    </select>
                    @error('produto_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-2">
                    <label for="quantidade_total">Quantidade</label>
                    <input
                        type="number"
                        min="{{ max(1, (int) $produtoChegada->quantidade_comprometida) }}"
                        id="quantidade_total"
                        name="quantidade_total"
                        class="form-control @error('quantidade_total') is-invalid @enderror"
                        value="{{ old('quantidade_total', $produtoChegada->quantidade_total) }}"
                        required
                    >
                    @error('quantidade_total')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if ((int) $produtoChegada->quantidade_comprometida > 0)
                        <small class="text-muted">Mínimo permitido: {{ (int) $produtoChegada->quantidade_comprometida }} (comprometida)</small>
                    @endif
                </div>

                <div class="col-md-3">
                    <label for="previsao_chegada">Previsão de chegada</label>
                    <input
                        type="date"
                        id="previsao_chegada"
                        name="previsao_chegada"
                        class="form-control @error('previsao_chegada') is-invalid @enderror"
                        value="{{ old('previsao_chegada', $produtoChegada->previsao_chegada?->format('Y-m-d')) }}"
                    >
                    @error('previsao_chegada')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label for="observacao">Observação</label>
                    <input
                        type="text"
                        id="observacao"
                        name="observacao"
                        class="form-control @error('observacao') is-invalid @enderror"
                        value="{{ old('observacao', $produtoChegada->observacao) }}"
                        maxlength="1000"
                    >
                    @error('observacao')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-success">Salvar alterações</button>
            </div>
        </form>
    </x-basic.content-page>
</x-app-layout>

@push('styles')
<link rel="stylesheet" href="{{ asset('adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endpush

@push('scripts')
@endpush
