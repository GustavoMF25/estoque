<x-app-layout>
    <x-basic.content-page :title="__('Editar Loja')" :class="'card-secondary'" :back="route('lojas.index')">
        <form action="{{ route('lojas.update', $loja->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('lojas.form', ['loja' => $loja])
            <button class="btn btn-success">Atualizar Loja</button>
        </form>
    </x-basic.content-page>
</x-app-layout>
