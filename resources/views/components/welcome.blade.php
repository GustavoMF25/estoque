<div class="row">
    @if(Auth::user()->isAdmin())
    <div class="col-lg-3 col-6">
        <livewire:produto.cadastrar-produto-card />
    </div>
    @endif
    <!-- <div class="col-lg-3 col-6">
        <livewire:produto.vender-produto-card />
    </div> -->
</div>