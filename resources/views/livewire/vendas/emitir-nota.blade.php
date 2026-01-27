<div>
    <ul class="nav nav-tabs" id="tabs-nota" role="tablist">
        <li class="nav-item active">
            <a class="nav-link active" id="tab-dados" data-toggle="tab" href="#dados" role="tab">
                Dados do cliente
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="tab-preview" data-toggle="tab" href="#preview" role="tab">
                Pré-visualização
            </a>
        </li>
    </ul>

    <div class="tab-content border-left border-right border-bottom p-3">
        <div class="tab-pane fade show active" id="dados" role="tabpanel">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Modelo da nota</label>
                        <select class="form-control" wire:model="modelo_id">
                            <option value="">Selecione...</option>
                            @foreach ($modelos as $modelo)
                                <option value="{{ $modelo->id }}">{{ $modelo->nome }}</option>
                            @endforeach
                        </select>
                        @error('modelo_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    @if ($modeloIcone)
                        <div class="mb-2 text-muted">
                            <i class="{{ $modeloIcone }}"></i> Ícone do modelo
                        </div>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nome</label>
                        <input type="text" class="form-control" wire:model.defer="cliente_nome">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Documento</label>
                        <input type="text" class="form-control" wire:model.defer="cliente_documento">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" wire:model.defer="cliente_email">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Telefone</label>
                        <input type="text" class="form-control" wire:model.defer="cliente_telefone">
                    </div>
                </div>
            </div>

            <hr>
            <h6>Endereço</h6>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>CEP</label>
                        <input type="text" class="form-control" wire:model.defer="cep" id="nota_cep" maxlength="8"
                            data-cep-lookup="true"
                            data-cep-rua="nota_rua"
                            data-cep-bairro="nota_bairro"
                            data-cep-cidade="nota_cidade"
                            data-cep-estado="nota_estado">
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group">
                        <label>Rua</label>
                        <input type="text" class="form-control" wire:model.defer="rua" id="nota_rua">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Número</label>
                        <input type="text" class="form-control" wire:model.defer="numero" id="nota_numero">
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group">
                        <label>Complemento</label>
                        <input type="text" class="form-control" wire:model.defer="complemento" id="nota_complemento">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Bairro</label>
                        <input type="text" class="form-control" wire:model.defer="bairro" id="nota_bairro">
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label>Cidade</label>
                        <input type="text" class="form-control" wire:model.defer="cidade" id="nota_cidade">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Estado</label>
                        <input type="text" class="form-control" wire:model.defer="estado" id="nota_estado" maxlength="2">
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="preview" role="tabpanel">
            <div class="border rounded p-2 bg-light">
                <div class="mb-2 text-muted">Pré-visualização (Frente)</div>
                <div class="p-2 bg-white rounded" style="min-height: 200px;">
                    {!! $previewFrente !!}
                </div>
                <hr>
                <div class="mb-2 text-muted">Pré-visualização (Verso)</div>
                <div class="p-2 bg-white rounded" style="min-height: 200px;">
                    {!! $previewVerso !!}
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end mt-3">
        <button class="btn btn-primary" wire:click="emitir">Emitir Nota</button>
    </div>

</div>
