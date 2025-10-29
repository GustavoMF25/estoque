<x-app-layout>
    <x-basic.content-page :title="__('Editar Dados da Empresa')" :class="'card-primary'">

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('empresa.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="nome" class="form-label">Nome Fantasia</label>
                <input type="text" name="nome" class="form-control" value="{{ old('nome', $empresas->nome) }}">
            </div>

            <div class="mb-3">
                <label for="razao_social" class="form-label">Razão Social</label>
                <input type="text" name="razao_social" class="form-control"
                    value="{{ old('razao_social', $empresas->razao_social) }}">
            </div>

            <div class="mb-3">
                <label for="cnpj" class="form-label">CNPJ</label>
                <input type="text" name="cnpj" class="form-control" value="{{ old('cnpj', $empresas->cnpj) }}">
            </div>

            <div class="mb-3">
                <label for="telefone" class="form-label">Telefone</label>
                <input type="text" name="telefone" class="form-control"
                    value="{{ old('telefone', $empresas->telefone) }}">
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $empresas->email) }}">
            </div>

            <div class="mb-3">
                <label for="endereco" class="form-label">Endereço</label>
                <input type="text" name="endereco" class="form-control"
                    value="{{ old('endereco', $empresas->endereco) }}">
            </div>

            <div class="mb-3">

                <div x-data="{ photoName: null, photoPreview: null }">
                    <div id="actions" class="row">
                        <div class="col-lg-6">
                            <div class="btn-group w-100">
                                <!-- Botão de escolher arquivo -->
                                <label class="btn btn-success col fileinput-button mb-0">
                                    <i class="fas fa-plus"></i>
                                    <span>Enviar nova logo</span>
                                    <input id="logo" name="logo" type="file" accept="image/png, image/jpeg"
                                        class="d-none" x-ref="photo"
                                        x-on:change="
                                            photoName = $refs.photo.files[0].name;
                                            const reader = new FileReader();
                                            reader.onload = (e) => {
                                                photoPreview = e.target.result;
                                            };
                                            reader.readAsDataURL($refs.photo.files[0]);
                                        " />
                                </label>

                                <!-- Botão de cancelar -->
                                <button type="button" class="btn btn-warning col cancel"
                                    x-on:click.prevent="
                                        photoName = null;
                                        photoPreview = null;
                                        $refs.photo.value = '';
                                    ">
                                    <i class="fas fa-times-circle"></i>
                                    <span>Cancelar</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Preview -->
                    <div class="mt-4" x-show="photoPreview" style="display: none;">
                        <label class="form-label">Pré-visualização da nova logo</label><br>
                        <img :src="photoPreview" class="rounded shadow-sm" width="150" height="80">
                    </div>

                    <!-- Logo atual -->
                    @if ($empresas->logo)
                        <div class="mt-3">
                            <label class="form-label">Logo atual</label><br>
                            <img src="{{ asset('storage/' . $empresas->logo) }}" class="rounded border" width="150"
                                height="80">
                        </div>
                    @endif
                </div>


                <x-input-error for="photo" class="mt-2" />
                {{-- <label for="logo" class="form-label">Logo</label><br>
                @if ($empresas->logo)
                    <img src="{{ asset('storage/' . $empresas->logo) }}" height="80" class="mb-2">
                @endif
                <input type="file" name="logo" class="form-control"> --}}
            </div>

            <button class="btn btn-success">Salvar</button>
        </form>

    </x-basic.content-page>
</x-app-layout>
