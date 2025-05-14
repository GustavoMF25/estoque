<div>
    <div class="mb-3">
        <strong>Nome:</strong> {{ $estoque->nome }}
    </div>

    <div class="mb-3">
        <strong>Descrição:</strong> {{ $estoque->descricao ?? '—' }}
    </div>

    <div class="mb-3">
        <strong>Quantidade máxima:</strong> {{ $estoque->quantidade_maxima }}
    </div>

    <div class="mb-3">
        <strong>Status:</strong>
        <span class="badge badge-{{ $estoque->status === 'ativo' ? 'success' : 'secondary' }}">
            {{ ucfirst($estoque->status) }}
        </span>
    </div>

    <div class="mb-3">
        <strong>Criado em:</strong> {{ $estoque->created_at->format('d/m/Y') }}
    </div>

    <hr>

    <div class="mt-3">
        <strong>Itens neste estoque:</strong> {{ $estoque->produtos_count ?? 0 }} produtos
    </div>

</div>
