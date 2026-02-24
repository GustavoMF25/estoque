<x-app-layout>
    <x-basic.content-page :title="__('Lojas')" :class="'card-secondary'" :btnCadastrarAdmin="['route' => route('lojas.create'), 'title' => 'Cadastrar Loja']">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="alert alert-info">
            Limite de lojas por empresa: <strong>{{ $limiteLojas }}</strong>.
            Cadastradas: <strong>{{ $lojasCount }}</strong>.
        </div>

        @livewire('loja-table')
    </x-basic.content-page>
</x-app-layout>
