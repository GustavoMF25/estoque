<div>
    <form wire:submit.prevent="salvar" id="salvar-editar-endereco">
        <div class="form-group">
            <label>Rótulo</label>
            <input type="text" wire:model.defer="rotulo" class="form-control" placeholder="Ex: Principal, Cobrança...">
        </div>

        <div class="form-group">
            <label>CEP</label>
            <input type="text" wire:model.defer="cep" class="form-control">
        </div>

        <div class="form-row">
            <div class="col-md-8">
                <label>Rua</label>
                <input type="text" wire:model.defer="rua" class="form-control">
            </div>
            <div class="col-md-4">
                <label>Número</label>
                <input type="text" wire:model.defer="numero" class="form-control">
            </div>
        </div>

        <div class="form-group mt-2">
            <label>Complemento</label>
            <input type="text" wire:model.defer="complemento" class="form-control">
        </div>

        <div class="form-row mt-2">
            <div class="col-md-4">
                <label>Bairro</label>
                <input type="text" wire:model.defer="bairro" class="form-control">
            </div>
            <div class="col-md-5">
                <label>Cidade</label>
                <input type="text" wire:model.defer="cidade" class="form-control">
            </div>
            <div class="col-md-3">
                <label>Estado</label>
                <input type="text" wire:model.defer="estado" class="form-control" maxlength="2">
            </div>
        </div>

    </form>
</div>
