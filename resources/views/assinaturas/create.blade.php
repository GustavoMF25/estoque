<x-app-layout>
    <x-basic.content-page :title="__('Assinaturas')" :class="'card-secondary'" :btnCadastrar="['route' => route('clientes.create'), 'title' => 'Cadastrar Cliente']">
        <form action="{{ route('assinaturas.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Empresa</label>
                    <select name="empresa_id" class="form-select" required>
                        <option value="">Selecione</option>
                        @foreach ($empresas as $empresa)
                            <option value="{{ $empresa->id }}">{{ $empresa->nome }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label>Plano</label>
                    <input type="text" name="plano" value="gestao_completa" class="form-control" required>
                </div>

                <div class="col-md-3 mb-3">
                    <label>Valor Mensal (R$)</label>
                    <input type="number" step="0.01" name="valor_mensal" value="149.00" class="form-control"
                        required>
                </div>

                <div class="col-md-3 mb-3">
                    <label>Data Início</label>
                    <input type="date" name="data_inicio" value="{{ date('Y-m-d') }}" class="form-control" required>
                </div>

                <div class="col-md-3 mb-3">
                    <label>Data Vencimento</label>
                    <input type="date" name="data_vencimento" value="{{ now()->addDays(30)->format('Y-m-d') }}"
                        class="form-control" required>
                </div>

                <div class="col-md-3 mb-3">
                    <label>Status</label>
                    <select name="status" class="form-select">
                        <option value="pendente">Pendente</option>
                        <option value="ativo">Ativo</option>
                        <option value="atrasado">Atrasado</option>
                        <option value="cancelado">Cancelado</option>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label>Método de Pagamento</label>
                    <select name="metodo_pagamento" class="form-select">
                        <option value="manual">Manual</option>
                        <option value="pix">Pix</option>
                        <option value="asaas">Asaas</option>
                        <option value="pagseguro">PagSeguro</option>
                    </select>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="mdi mdi-content-save"></i> Salvar
                </button>
                <a href="{{ route('assinaturas.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </x-basic.content-page>
</x-app-layout>
