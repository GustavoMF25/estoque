@php($funcionalidade = $funcionalidade ?? null)

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="nome">Nome</label>
        <input id="nome" name="nome" type="text" class="form-control @error('nome') is-invalid @enderror"
            value="{{ old('nome', $funcionalidade->nome ?? '') }}" required>
        @error('nome')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="slug">Slug</label>
        <input id="slug" name="slug" type="text" class="form-control @error('slug') is-invalid @enderror"
            value="{{ old('slug', $funcionalidade->slug ?? '') }}" required>
        @error('slug')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="modulo">Módulo</label>
        <input id="modulo" name="modulo" type="text" class="form-control @error('modulo') is-invalid @enderror"
            value="{{ old('modulo', $funcionalidade->modulo ?? '') }}" placeholder="Ex: Módulo Comercial">
        @error('modulo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="rota">Rota (name)</label>
        <input id="rota" name="rota" type="text" class="form-control @error('rota') is-invalid @enderror"
            value="{{ old('rota', $funcionalidade->rota ?? '') }}" placeholder="Ex: vendas.index">
        @error('rota')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="icone">Ícone</label>
        <input id="icone" name="icone" type="text" class="form-control @error('icone') is-invalid @enderror"
            value="{{ old('icone', $funcionalidade->icone ?? '') }}" placeholder="Ex: fas fa-shopping-cart">
        @error('icone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="ordem">Ordem</label>
        <input id="ordem" name="ordem" type="number" min="0"
            class="form-control @error('ordem') is-invalid @enderror"
            value="{{ old('ordem', $funcionalidade->ordem ?? 0) }}">
        @error('ordem')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-12 mb-3">
        <label for="descricao">Descrição</label>
        <textarea id="descricao" name="descricao" rows="2"
            class="form-control @error('descricao') is-invalid @enderror">{{ old('descricao', $funcionalidade->descricao ?? '') }}</textarea>
        @error('descricao')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-12">
        <div class="form-check form-check-inline">
            <input id="visivel_menu" name="visivel_menu" type="checkbox" value="1" class="form-check-input"
                {{ old('visivel_menu', $funcionalidade->visivel_menu ?? true) ? 'checked' : '' }}>
            <label for="visivel_menu" class="form-check-label">Exibir no menu</label>
        </div>
        <div class="form-check form-check-inline">
            <input id="ativo" name="ativo" type="checkbox" value="1" class="form-check-input"
                {{ old('ativo', $funcionalidade->ativo ?? true) ? 'checked' : '' }}>
            <label for="ativo" class="form-check-label">Funcionalidade ativa</label>
        </div>
    </div>
</div>
