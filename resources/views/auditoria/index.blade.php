<x-app-layout>
    <x-basic.content-page :title="__('Auditoria')" :class="'card-secondary'">
        <div class="container">
            <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="h3 mb-1">Auditoria do Sistema</h1>
                    <p class="text-muted mb-0">Eventos registrados no banco de dados em tempo real.</p>
                </div>
                <a href="{{ route('auditoria.index') }}" class="btn btn-outline-secondary btn-sm">Atualizar</a>
            </div>

            @livewire('audit-log-table')
        </div>
    </x-basic.content-page>
</x-app-layout>
