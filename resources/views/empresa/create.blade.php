<x-app-layout>
    <x-basic.content-page :title="__('Cadastrar Cliente')" :class="'card-secondary'">
        <form action="{{ route('empresas.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Nome *</label>
                    <input type="text" name="nome" class="form-control" required value="{{ old('nome') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Razão Social</label>
                    <input type="text" name="razao_social" class="form-control" value="{{ old('razao_social') }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label>CNPJ *</label>
                    <input type="text" name="cnpj" class="form-control" required value="{{ old('cnpj') }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label>Telefone</label>
                    <input type="text" name="telefone" class="form-control" value="{{ old('telefone') }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                </div>

                <div class="col-md-8 mb-3">
                    <label>Endereço</label>
                    <input type="text" name="endereco" class="form-control" value="{{ old('endereco') }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label>Logo (opcional)</label>
                    <input type="file" name="logo" class="form-control" accept="image/*">
                </div>

                <div class="col-md-12 mb-3">
                    <label>Configurações Extras (JSON)</label>
                    <textarea name="configuracoes" class="form-control" rows="3"
                        placeholder='{"estoque_minimo": 10, "notificacoes": true}'>{{ old('configuracoes') }}</textarea>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="mdi mdi-content-save"></i> Salvar
                </button>
                <a href="{{ route('empresas.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </x-basic.content-page>
</x-app-layout>
