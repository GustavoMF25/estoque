<div>
    <form wire:submit.prevent="save" id="{{ $formId }}">
        <div class="form-group">
            <label for="protocolo">Protocolo (Número do Pedido)</label>
            <input type="text" wire:model.defer="protocolo" id="protocolo" class="form-control"
                placeholder="Informe o protocolo do pedido">
            @error('protocolo')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </form>
</div>
