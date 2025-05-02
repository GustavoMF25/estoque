<x-app-layout>

    <x-basic.content-page :title="__('Usuarios')" :class="'card-secondary'" :btnCadastrarAdmin="['route' => route('usuarios.create'), 'title' => 'Cadastrar usuario']">


        <div class="container">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @livewire('user-table')

        </div>
    </x-basic.content-page>
</x-app-layout>
