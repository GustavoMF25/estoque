@php($loja = $loja ?? null)
@php($contatos = old('contatos', $loja->contatos ?? []))

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="nome" class="form-label">Nome Fantasia</label>
        <input type="text" id="nome" name="nome" class="form-control @error('nome') is-invalid @enderror"
            value="{{ old('nome', $loja->nome ?? '') }}" required>
        @error('nome')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="razao_social" class="form-label">Razão Social</label>
        <input type="text" id="razao_social" name="razao_social"
            class="form-control @error('razao_social') is-invalid @enderror"
            value="{{ old('razao_social', $loja->razao_social ?? '') }}">
        @error('razao_social')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="cnpj" class="form-label">CNPJ</label>
        <input type="text" id="cnpj" name="cnpj" class="form-control @error('cnpj') is-invalid @enderror"
            value="{{ old('cnpj', $loja->cnpj ?? '') }}" placeholder="00.000.000/0000-00">
        @error('cnpj')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="telefone" class="form-label">Telefone</label>
        <input type="text" id="telefone" name="telefone"
            class="form-control @error('telefone') is-invalid @enderror"
            value="{{ old('telefone', $loja->telefone ?? '') }}">
        @error('telefone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-12 mb-3">
        <label class="form-label">Contatos adicionais (JSON)</label>
        <div id="contatos-wrapper">
            @forelse ($contatos as $i => $contato)
                <div class="row g-2 align-items-end mb-2 contato-row">
                    <div class="col-md-5">
                        <label class="form-label">Motivo</label>
                        <input type="text" name="contatos[{{ $i }}][name]" class="form-control"
                            value="{{ $contato['name'] ?? '' }}" placeholder="Ex: Assistência">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Número</label>
                        <input type="text" name="contatos[{{ $i }}][numero]" class="form-control"
                            value="{{ $contato['numero'] ?? '' }}" placeholder="(21)9999999999">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-outline-danger btn-block contato-remove">Remover</button>
                    </div>
                </div>
            @empty
                <div class="row g-2 align-items-end mb-2 contato-row">
                    <div class="col-md-5">
                        <label class="form-label">Motivo</label>
                        <input type="text" name="contatos[0][name]" class="form-control" placeholder="Ex: Assistência">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Número</label>
                        <input type="text" name="contatos[0][numero]" class="form-control" placeholder="(21)9999999999">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-outline-danger btn-block contato-remove">Remover</button>
                    </div>
                </div>
            @endforelse
        </div>
        <button type="button" id="contato-add" class="btn btn-outline-primary btn-sm">Adicionar contato</button>
        <small class="form-text text-muted">
            Estrutura salva em JSON: <code>{"name":"assistencia","numero":"(21)9999999999"}</code>
        </small>
        @error('contatos')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
        @error('contatos.*.name')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
        @error('contatos.*.numero')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="email" class="form-label">E-mail</label>
        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
            value="{{ old('email', $loja->email ?? '') }}">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-12 mb-3">
        <label for="endereco" class="form-label">Endereço</label>
        <input type="text" id="endereco" name="endereco"
            class="form-control @error('endereco') is-invalid @enderror"
            value="{{ old('endereco', $loja->endereco ?? '') }}">
        @error('endereco')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-12 mb-3">
        <label for="logo" class="form-label">Logo da Loja</label>
        <input type="file" id="logo" name="logo" accept="image/png,image/jpeg,image/webp"
            class="form-control @error('logo') is-invalid @enderror">
        @error('logo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    @if (!empty($loja?->logo))
        <div class="col-md-12 mb-3">
            <label class="form-label">Logo atual</label><br>
            <img src="{{ asset('storage/' . $loja->logo) }}" alt="Logo da loja" width="160" class="rounded border">
        </div>
    @endif
</div>

@push('scripts')
    <script>
        (function() {
            const wrapper = document.getElementById('contatos-wrapper');
            const addBtn = document.getElementById('contato-add');
            if (!wrapper || !addBtn) return;

            function renumerar() {
                const rows = wrapper.querySelectorAll('.contato-row');
                rows.forEach((row, idx) => {
                    const motivo = row.querySelector('input[name*="[name]"]');
                    const numero = row.querySelector('input[name*="[numero]"]');
                    if (motivo) motivo.name = `contatos[${idx}][name]`;
                    if (numero) numero.name = `contatos[${idx}][numero]`;
                });
            }

            function bindRemove(button) {
                button.addEventListener('click', function() {
                    const rows = wrapper.querySelectorAll('.contato-row');
                    if (rows.length === 1) {
                        rows[0].querySelectorAll('input').forEach((input) => input.value = '');
                        return;
                    }
                    this.closest('.contato-row')?.remove();
                    renumerar();
                });
            }

            wrapper.querySelectorAll('.contato-remove').forEach(bindRemove);

            addBtn.addEventListener('click', function() {
                const idx = wrapper.querySelectorAll('.contato-row').length;
                const row = document.createElement('div');
                row.className = 'row g-2 align-items-end mb-2 contato-row';
                row.innerHTML = `
                    <div class="col-md-5">
                        <label class="form-label">Motivo</label>
                        <input type="text" name="contatos[${idx}][name]" class="form-control" placeholder="Ex: Assistência">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Número</label>
                        <input type="text" name="contatos[${idx}][numero]" class="form-control" placeholder="(21)9999999999">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-outline-danger btn-block contato-remove">Remover</button>
                    </div>
                `;
                wrapper.appendChild(row);
                const btn = row.querySelector('.contato-remove');
                if (btn) bindRemove(btn);
            });
        })();
    </script>
@endpush
