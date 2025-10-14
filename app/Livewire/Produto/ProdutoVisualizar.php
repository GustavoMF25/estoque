<?php

namespace App\Livewire\Produto;

use App\Models\Produto;
use App\Models\ProdutosAgrupados;
use Livewire\Component;
use Livewire\Attributes\On;

class ProdutoVisualizar extends Component
{
    public $nome;
    public $estoque_id;
    public $ultima_movimentacao;
    public $image;
    public $produto;
    public $qtdVendidos;
    public $valorRecebido;

    // protected $listeners = [
    //     'refreshProdutoVisualizar' => '$refresh',
    // ];

    public function mount($nome = null, $estoque_id = null, $ultima_movimentacao = null)
    {
        $this->nome = $nome;
        $this->estoque_id = $estoque_id;
        $this->ultima_movimentacao = $ultima_movimentacao;
        $this->loadData();
    }

    public function loadData(): void
    {
        $this->produto = ProdutosAgrupados::where('nome', 'like', "%{$this->nome}%")
            ->where('ultima_movimentacao', $this->ultima_movimentacao)
            ->first();
        $this->qtdVendidos = ProdutosAgrupados::where('nome', 'like', "%{$this->nome}%")
            ->where('ultima_movimentacao', 'saida')
            ->first()->quantidade_produtos;
        $this->valorRecebido =  $this->qtdVendidos * $this->produto->preco;

        $this->image = Produto::where('nome', 'like', "%{$this->nome}%")
            ->select('imagem')
            ->first();
    }

    public function render()
    {
        return view('livewire.produto.produto-visualizar');
    }
}
