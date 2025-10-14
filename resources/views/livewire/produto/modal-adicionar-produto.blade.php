<div>
    @if ($nome)
    <form wire:submit.prevent="adicionar" id="adicionarProduto" enctype="multipart/form-data">
        <div class="row">
            <input type="text" class="form-control" value="{{ $nome }}" hidden wire:model.defer="nome_atual">

            <div class="col-md-12 mb-3">
                <label>Quantidade</label>
                <input type="number" wire:model.defer="quantidade" class="form-control" min="0">
                @error('quantidade') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
    </form>
    @endif
</div>