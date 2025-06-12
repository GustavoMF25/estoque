<div>
    <form wire:submit.prevent="salvar" id="salvar-editar-produto">
        <div class="form-group">
            <label>Nome</label>
            <input type="text" class="form-control" wire:model.defer="nome">
            @error('nome') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group">
            <label>Unidade</label>
            <input type="text" class="form-control" wire:model.defer="unidade">
        </div>

        <div class="form-group">
            <label>Preço</label>
            <input type="number" step="0.01" class="form-control" wire:model.defer="preco">
            @error('preco') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group">
            <label>Estoque</label>
            <select class="form-control" wire:model.defer="estoque_id">
                @foreach ($estoques as $id => $nome)
                    <option value="{{ $id }}">{{ $nome }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Imagem</label><br>
            @if ($imagem)
                <img src="{{ $imagem->temporaryUrl() }}" class="img-thumbnail mb-2" style="max-width: 150px;">
            @elseif ($imagemPreview)
                <img src="{{ asset('storage/' . $imagemPreview) }}" class="img-thumbnail mb-2" style="max-width: 150px;">
            @endif
            <input type="file" wire:model="imagem" class="form-control-file">
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" wire:model.defer="ativo" class="form-check-input" id="ativo">
            <label for="ativo" class="form-check-label">Ativo</label>
        </div>

        {{-- <button type="submit" class="btn btn-primary">Salvar</button> --}}
    </form>
</div>
