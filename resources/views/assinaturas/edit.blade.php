<x-app-layout>
    <x-basic.content-page :title="__('Assinaturas')" :class="'card-secondary'" :btnCadastrar="['route' => route('clientes.create'), 'title' => 'Cadastrar Cliente']">
        <form action="{{ route('assinaturas.update', $assinatura->id) }}" method="PUT">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Empresa</label>
                    <select name="empresa_id" class="form-select" disabled>
                        <option value="{{ $assinatura->empresa->id }}">
                            {{ $assinatura->empresa->nome }}
                        </option>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label>Plano</label>
                    <input type="text" name="plano" value="{{ $assinatura->plano }}" class="form-control" required>
                </div>

                <div class="col-md-3 mb-3">
                    <label>Valor Mensal (R$)</label>
                    <input type="number" step="0.01" name="valor_mensal" value="{{ $assinatura->valor_mensal }}"
                        class="form-control" required>
                </div>

                <div class="col-md-3 mb-3">
                    <label>Data Início</label>
                    <input type="date" name="data_inicio" value="{{ $assinatura->data_inicio->format('Y-m-d') }}"
                        class="form-control" required>
                </div>

                @if ($assinatura->periodicidade !== 'vitalicio')
                    <div class="col-md-3 mb-3">
                        <label>Data Vencimento</label>
                        <input type="date" name="data_vencimento"
                            value="{{ $assinatura->data_vencimento?->format('Y-m-d') }}" class="form-control">
                    </div>
                @endif

                <div class="col-md-3 mb-3">
                    <label>Status</label>
                    <select name="status" class="form-select">
                        @foreach (['pendente', 'ativo', 'atrasado', 'cancelado'] as $status)
                            <option value="{{ $status }}" {{ $assinatura->status === $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label>Periodicidade</label>
                    <select name="periodicidade" class="form-select">
                        @foreach (['mensal', 'trimestral', 'anual', 'vitalicio'] as $periodicidade)
                            <option value="{{ $periodicidade }}"
                                {{ $assinatura->periodicidade === $periodicidade ? 'selected' : '' }}>
                                {{ ucfirst($periodicidade) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label>Método de Pagamento</label>
                    <select name="metodo_pagamento" class="form-select">
                        @foreach (['manual', 'pix', 'asaas', 'pagseguro'] as $metodo)
                            <option value="{{ $metodo }}"
                                {{ $assinatura->metodo_pagamento === $metodo ? 'selected' : '' }}>
                                {{ ucfirst($metodo) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="mdi mdi-content-save"></i> Atualizar
                </button>
                <a href="{{ route('assinaturas.index') }}" class="btn btn-secondary">Voltar</a>
            </div>
        </form>
    </x-basic.content-page>
</x-app-layout>
