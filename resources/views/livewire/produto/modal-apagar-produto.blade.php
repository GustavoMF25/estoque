<div>
    <form wire:submit.prevent="apagar" id="apagarProduto" enctype="multipart/form-data">
        <p class="mb-0">
            Tem certeza de que deseja remover o produto
            <strong>{{ $nome }}</strong>?
        </p>
        <small class="text-muted">
            Todas as unidades serão marcadas como <strong>indisponíveis</strong>.
            Esta ação não pode ser desfeita.
        </small>
    </form>
</div>
