@php
    $c = $cliente ?? null;
    $e = $c?->enderecoPadrao;
@endphp

<div class="form-group">
    <label>Nome</label>
    <input type="text" name="nome" value="{{ old('nome', $c->nome ?? '') }}" class="form-control" required>
</div>
<div class="form-group">
    <label>Email</label>
    <input type="email" name="email" value="{{ old('email', $c->email ?? '') }}" class="form-control">
</div>
<div class="form-group">
    <label>Telefone</label>
    <input type="text" name="telefone" value="{{ old('telefone', $c->telefone ?? '') }}" class="form-control">
</div>
<div class="form-group">
    <label>Documento</label>
    <input type="text" name="documento" value="{{ old('documento', $c->documento ?? '') }}" class="form-control">
</div>
<div class="form-group">
    <label>Observações</label>
    <textarea name="observacoes" class="form-control">{{ old('observacoes', $c->observacoes ?? '') }}</textarea>
</div>

<hr>
<h5>Endereço</h5>
<div class="form-row">
    <div class="col">
        <label>CEP</label>
        <input type="text" name="endereco[cep]" value="{{ old('endereco.cep', $e->cep ?? '') }}" class="form-control">
    </div>
    <div class="col-6">
        <label>Rua</label>
        <input type="text" name="endereco[rua]" value="{{ old('endereco.rua', $e->rua ?? '') }}" class="form-control">
    </div>
    <div class="col">
        <label>Número</label>
        <input type="text" name="endereco[numero]" value="{{ old('endereco.numero', $e->numero ?? '') }}"
            class="form-control">
    </div>
</div>
<div class="form-row form-group mt-2">
    <div class="col">
        <label>Bairro</label>
        <input type="text" name="endereco[bairro]" value="{{ old('endereco.bairro', $e->bairro ?? '') }}"
            class="form-control">
    </div>
    <div class="col">
        <label>Cidade</label>
        <input type="text" name="endereco[cidade]" value="{{ old('endereco.cidade', $e->cidade ?? '') }}"
            class="form-control">
    </div>
    <div class="col">
        <label>Estado</label>
        <input type="text" name="endereco[estado]" value="{{ old('endereco.estado', $e->estado ?? '') }}"
            class="form-control">
    </div>
</div>
