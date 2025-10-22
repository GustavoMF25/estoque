<?php

namespace App\Livewire\Produto;

use App\Models\Categoria;
use App\Models\Estoque;
use App\Models\Fabricante;
use App\Models\Produto;
use App\Models\ProdutosUnidades;
use Livewire\Component;



class CadastrarProdutoCard extends Component
{

    public int $quantidade;
    public $categorias;
    public $fabricantes;
    public string $view;

    public function mount()
    {
        $this->quantidade = ProdutosUnidades::where('status', 'disponivel')->count();
        $estoques = Estoque::all();

        $this->categorias = Categoria::where('ativo', true)->orderBy('nome')->get();
        $this->fabricantes = Fabricante::orderBy('nome')->get();

        $this->view = $view = view('livewire.produto.modal-cadastrar-produto', ['estoques' => $estoques, 'categorias' =>  $this->categorias, 'fabricantes' => $this->fabricantes])->render();
    }

    public function render()
    {
        return view('livewire.produto.cadastrar-produto-card');
    }
}
