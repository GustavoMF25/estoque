<x-app-layout>
    <x-basic.content-page :title="__('Assinaturas')" :class="'card-secondary'" :btnCadastrar="['route' => route('clientes.create'), 'title' => 'Cadastrar Cliente']">
        <form action="{{ route('assinaturas.store', $empresas->id) }}" method="POST">
            @csrf

            {{-- Empresa --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Empresa</label>
                <input type="text" class="form-control" value="{{ $empresas->nome }}" readonly>
            </div>

            {{-- Plano --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Nome do Plano</label>
                <input type="text" name="plano_nome" class="form-control" placeholder="Ex: Plano Mensal, Premium, etc"
                    required>
            </div>

            {{-- Valor --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Valor (R$)</label>
                <input type="number" name="valor" class="form-control" step="0.01" placeholder="Ex: 59.90"
                    required>
            </div>

            {{-- Duração --}}
            <div class="mb-3">
                <label class="form-label">Periodicidade</label>
                <select name="periodicidade" class="form-select" required>
                    <option value="mensal" selected>Mensal</option>
                    <option value="trimestral">Trimestral</option>
                    <option value="anual">Anual</option>
                </select>
            </div>

            {{-- Status inicial --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select" required>
                    <option value="ativo">Ativo</option>
                    <option value="pendente">Pendente</option>
                </select>
            </div>

            {{-- Botões --}}
            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Salvar Assinatura
                </button>
            </div>
        </form>
    </x-basic.content-page>
</x-app-layout>
