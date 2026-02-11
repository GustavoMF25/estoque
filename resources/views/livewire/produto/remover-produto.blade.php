<div>
    @if ($nome)
        <form wire:submit.prevent="remover" id="removerProduto" enctype="multipart/form-data">
            <div class="row">
                <input type="text" class="form-control" value="{{ $nome }}" hidden wire:model.defer="nome_atual">

                <div class="col-md-6 mb-3">
                    <label>Quantidade</label>
                    <input type="number" wire:model.defer="quantidade" class="form-control" min="0"
                        max="{{ $qtdMax }}">
                    @error('quantidade')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6 mb-4">
                    <label>Motivo</label>
                    <select wire:model.defer="novo_status" class="form-control">
                        <option value="">Selecione...</option>
                        <option value="vendido">Vendito</option>
                        <option value="reservado">Reservado</option>
                        <option value="defeito">Defeito</option>

                    </select>
                    @error('estoque_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-12 mb-3">
                    <label>Observação</label>
                    <textarea wire:model.defer="observacao" class="form-control">
                </textarea>
                    @error('quantidade')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </form>
    @endif
</div>