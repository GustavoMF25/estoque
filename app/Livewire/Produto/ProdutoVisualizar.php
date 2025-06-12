<?php

namespace App\Livewire\Produto;

use App\Models\Produto;
use Livewire\Component;

class ProdutoVisualizar extends Component
{
    public $nome;
    public $estoque_id;
    public $ultima_movimentacao;
    public $image;

    public function mount($nome = null, $estoque_id = null, $ultima_movimentacao = null)
    {
        $this->nome = $nome;
        $this->estoque_id = $estoque_id;
        $this->ultima_movimentacao = $ultima_movimentacao;

        $this->image = Produto::where('nome' , 'like', "%".$nome."%")->select('imagem')->first();
    }

    public function render()
    {
        return view('livewire.produto.produto-visualizar');
    }
}
