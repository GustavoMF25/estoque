<div>
    @if ($nome)
    <form wire:submit.prevent="atualizar" id="atualizarProduto" enctype="multipart/form-data">
        <div class="row">
            <input type="text" class="form-control" value="{{ $nome }}" hidden wire:model.defer="nome_atual">

            <div class="col-md-4 mb-3">
                <label>Nome do Produto</label>
                <input type="text" class="form-control" value="{{ $nome }}" wire:model.defer="nome">
            </div>

            <div class="col-md-4 mb-3">
                <label>Preço</label>
                <input type="number" wire:model.defer="preco" class="form-control" step="0.01">
                @error('preco') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label>Quantidade</label>
                <input type="number" wire:model.defer="quantidade" class="form-control" min="0">
                @error('quantidade') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="col-md-3 mb-3">
                <label>Imagem do Produto</label>
                <input type="file" wire:model="imagem" class="form-control" accept="image/*">
                @error('imagem') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="col-md-3 mb-3">
                <label>Estoque</label>
                <select wire:model.defer="estoque_id" class="form-control">
                    <option value="">Selecione...</option>
                    @foreach ($estoques as $estoque)
                    <option value="{{ $estoque->id }}">{{ $estoque->nome }}</option>
                    @endforeach
                </select>
                @error('estoque_id') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="col-md-3 mb-3">
                <label>Categoria</label>
                <select wire:model.defer="categoria" class="form-control">
                    <option value="">Selecione...</option>
                    @foreach ($categorias as $categoriaItem)
                    <option value="{{ $categoriaItem->id }}">{{ $categoriaItem->nome }}</option>
                    @endforeach
                </select>
                @error('categoria') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group col-md-3 mb-3">
                <label for="fabrricante">Fabricante</label>
                <select wire:model.defer="fabricante_id" name="fabricante_id" class="form-control select2">
                    <option value="">Sem fabricantes</option>
                    @forelse ($fabricantes as $fabricante)
                    <option value="{{ $fabricante->id }}">{{ $fabricante->nome }}</option>
                    @empty
                    @endforelse
                </select>
                @error('fabricante') <span class="text-danger">{{ $message }}</span> @enderror
            </div>


        </div>
    </form>
    @endif
</div>