<div class="small-box bg-success">
    <div class="inner">
        <h3>{{ $quantidade }}</h3>

        <p>Vendas</p>
    </div>
    <div class="icon">
        <i class="fas fa-solid fa-store"></i>
    </div>
    <a href="#"
        onclick="
        abrirModalDinamico({
            titulo: 'Cadastrar Venda/Saida',
            formId: 'formVenda',
            componente: 'produto.modal-cadastrar-venda',
        })
    "
        class="small-box-footer">
        Cadastrar <i class="fas fa-arrow-circle-right"></i>
    </a>
</div>
