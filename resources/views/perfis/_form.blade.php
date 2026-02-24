@php($perfil = $perfil ?? null)

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="nome">Nome</label>
        <input id="nome" name="nome" type="text" class="form-control @error('nome') is-invalid @enderror"
            value="{{ old('nome', $perfil->nome ?? '') }}" required>
        @error('nome')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="slug">Slug</label>
        <input id="slug" name="slug" type="text" class="form-control @error('slug') is-invalid @enderror"
            value="{{ old('slug', $perfil->slug ?? '') }}" {{ ($perfil->slug ?? null) === 'admin' ? 'readonly' : '' }} required>
        @error('slug')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-12 mb-3">
        <label for="descricao">Descrição</label>
        <textarea id="descricao" name="descricao" rows="2"
            class="form-control @error('descricao') is-invalid @enderror">{{ old('descricao', $perfil->descricao ?? '') }}</textarea>
        @error('descricao')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-12">
        <div class="form-check">
            <input id="ativo" name="ativo" type="checkbox" value="1" class="form-check-input"
                {{ old('ativo', $perfil->ativo ?? true) ? 'checked' : '' }}>
            <label for="ativo" class="form-check-label">Perfil ativo</label>
        </div>
    </div>
</div>
