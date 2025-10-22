<?php

namespace App\Livewire\Produto;

use App\Models\Produto;
use App\Models\ProdutosAgrupados;
use App\Models\ProdutosUnidades;
use Livewire\Component;
use Livewire\Attributes\On;

class ProdutoVisualizar extends Component
{
    public $nome;
    public $id;
    public $estoque_id;
    public $ultima_movimentacao;
    public $image;
    public $produto;
    public $qtdVendidos;
    public $qtdDisponiveis;
    public $valorRecebido;


    public function mount($id = null, $nome = null, $estoque_id = null, $ultima_movimentacao = null)
    {
        $this->nome = $nome;
        $this->estoque_id = $estoque_id;
        $this->ultima_movimentacao = $ultima_movimentacao;
        $this->id = $id;
        $this->loadData();
    }

    public function loadData(): void
    {
        $this->produto = Produto::find($this->id);

        $this->qtdVendidos = ProdutosUnidades::where('produto_id', $this->produto->id)->Vendidas()->count();
        $this->qtdDisponiveis = ProdutosUnidades::where('produto_id', $this->produto->id)->Disponiveis()->count();

        $this->valorRecebido = $this->qtdVendidos * $this->produto->preco;
        $this->image = $this->produto->imagem ?? null;
    }

    public function render()
    {
        return view('livewire.produto.produto-visualizar');
    }
}
