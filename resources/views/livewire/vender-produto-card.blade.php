<div class="small-box bg-success">
    <div class="inner">
        <h3>{{$quantidade}}</h3>

        <p>Vendas</p>
    </div>
    <div class="icon">
        <i class="fas fa-solid fa-store"></i>
    </div>
    <a href="#"  onclick="
        window.dispatchEvent(new CustomEvent('abrirModal', {
            detail: {
                titulo: 'Cadastrar Venda/Saida',
                formId: 'formVenda',
                conteudo: {{ \Illuminate\Support\Js::from($view) }},
            }
        }));
        $('#modal-sm').modal('show');
    " class="small-box-footer">
        Cadastrar <i class="fas fa-arrow-circle-right"></i>
    </a>
</div>