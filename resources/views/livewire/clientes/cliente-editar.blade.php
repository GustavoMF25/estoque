<div>
    <form wire:submit.prevent="salvar" id="salvar-editar-cliente">
        <div class="form-group">
            <label>Nome</label>
            <input type="text" wire:model.defer="nome" class="form-control">
            @error('nome')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" wire:model.defer="email" class="form-control">
            @error('email')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label>Telefone</label>
            <input type="text" wire:model.defer="telefone" class="form-control">
        </div>

        <div class="form-group">
            <label>Documento</label>
            <input type="text" wire:model.defer="documento" class="form-control">
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" wire:model="ativo" id="ativo">
            <label class="form-check-label" for="ativo">
                Ativo
            </label>
        </div>

        <div class="form-group">
            <label>Observações</label>
            <textarea wire:model.defer="observacoes" class="form-control" rows="3"></textarea>
        </div>

    </form>
</div>
