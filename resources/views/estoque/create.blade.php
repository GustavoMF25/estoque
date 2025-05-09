<x-app-layout>
    <x-basic.content-page :title="__('Cadastrar Estoque')" :class="'card-secondary'" :back="route('estoques.index')">
        <form action="{{ route('estoques.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nome" class="form-label">Nome do Estoque</label>
                    <input type="text" name="nome" id="nome"
                        class="form-control @error('nome') is-invalid @enderror" value="{{ old('nome') }}" required>
                    @error('nome')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="localizacao" class="form-label">Localização</label>
                    <input type="text" name="localizacao" id="localizacao"
                        class="form-control @error('localizacao') is-invalid @enderror" value="{{ old('localizacao') }}"
                        required>
                    @error('localizacao')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label for="quantidade_maxima" class="form-label">Quantidade Máxima</label>
                    <input type="number" name="quantidade_maxima" id="quantidade_maxima"
                        class="form-control @error('quantidade_maxima') is-invalid @enderror" min="0"
                        value="{{ old('quantidade_maxima', 0) }}">
                    @error('quantidade_maxima')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-12 mb-3">
                    <label for="descricao" class="form-label">Descrição</label>
                    <textarea name="descricao" id="descricao" class="form-control @error('descricao') is-invalid @enderror" rows="3">{{ old('descricao') }}</textarea>
                    @error('descricao')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <x-button.save>
                {{ __('Saved.') }}
            </x-button.save>
        </form>
    </x-basic.content-page>
</x-app-layout>
