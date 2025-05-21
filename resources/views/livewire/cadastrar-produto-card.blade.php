<div class="small-box bg-info">
    <div class="inner">
        <h3>{{$quantidade}}</h3>

        <p>Produtos disponíveis</p>
    </div>
    <div class="icon">
        <i class="fas fa-solid fa-couch"></i>
    </div>
    <a href="#"  onclick="
        window.dispatchEvent(new CustomEvent('abrirModal', {
            detail: {
                titulo: 'Cadastrar produto',
                formId: 'cadastrarProduto',
                conteudo: {{ \Illuminate\Support\Js::from($view) }},
            }
        }));
        $('#modal-sm').modal('show');
    " class="small-box-footer">
        Cadastrar <i class="fas fa-arrow-circle-right"></i>
    </a>
</div>