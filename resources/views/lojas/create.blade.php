<x-app-layout>
    <x-basic.content-page :title="__('Cadastrar Loja')" :class="'card-secondary'" :back="route('lojas.index')">
        <form action="{{ route('lojas.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('lojas.form')
            <button class="btn btn-success">Salvar Loja</button>
        </form>
    </x-basic.content-page>
</x-app-layout>
