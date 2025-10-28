<div class="row">
    @if(Auth::user()->isAdmin())
        <div class="col-lg-3 col-6">
            <livewire:produto.cadastrar-produto-card />
        </div>
    @endif
    @if(auth()->user()->perfil == 'vendedor')
        <livewire:produto.catalogo-produto />
    @endif
    <!-- <div class="col-lg-3 col-6">
        <livewire:produto.vender-produto-card />
    </div> -->
</div>