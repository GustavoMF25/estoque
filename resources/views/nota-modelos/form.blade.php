@php
    $m = $modelo ?? null;
@endphp

<div class="form-group">
    <label>Nome</label>
    <input type="text" name="nome" class="form-control" required value="{{ old('nome', $m->nome ?? '') }}">
</div>

<div class="form-group">
    <label>Ícone (FontAwesome)</label>
    <input type="text" name="icone" class="form-control" value="{{ old('icone', $m->icone ?? '') }}"
        placeholder="Ex: fas fa-file-alt">
</div>

<div class="form-group">
    <label>Conteúdo (Frente)</label>
    <textarea name="conteudo_frente" class="form-control summernote" rows="8" required>{{ old('conteudo_frente', $m->conteudo_frente ?? '') }}</textarea>
    <small class="text-muted">
        Use placeholders como @verbatim{{cliente_nome}}, {{cliente_documento}}, {{endereco_rua}}@endverbatim.
    </small>
</div>

<div class="form-group">
    <label>Conteúdo (Verso)</label>
    <textarea name="conteudo_verso" class="form-control" rows="8" readonly>{{ old('conteudo_verso', $m->conteudo_verso ?? '') }}</textarea>
    <small class="text-muted">O verso é fixo e seguirá o modelo padrão da nota.</small>
</div>

<div class="form-group">
    <label>Ativo</label>
    <select name="ativo" class="form-control" required>
        <option value="1" {{ (old('ativo', $m->ativo ?? 1) == 1) ? 'selected' : '' }}>Sim</option>
        <option value="0" {{ (old('ativo', $m->ativo ?? 1) == 0) ? 'selected' : '' }}>Não</option>
    </select>
</div>

@push('styles')
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/summernote/summernote-bs4.min.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('adminlte/plugins/summernote/summernote-bs4.min.js') }}"></script>
    <script>
        (function() {
            if (typeof $ === 'undefined' || !$('.summernote').length) return;
            $('.summernote').summernote({
                height: 260,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'table']],
                    ['view', ['codeview']]
                ]
            });
        })();
    </script>
@endpush
