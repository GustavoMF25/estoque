<?php

namespace App\Livewire\Produto;

use App\Models\Categoria;
use App\Models\Estoque;
use App\Models\Produto;
use Livewire\Component;



class CadastrarProdutoCard extends Component
{

    public int $quantidade;
    public $categorias;
    public string $view;

    public function mount()
    {
        $this->quantidade = Produto::whereHas('ultimaMovimentacao', function ($query) {
            $query->where('tipo', 'disponivel');
        })->count();
        $estoques = Estoque::all();

        $this->categorias = Categoria::where('ativo', true)->orderBy('nome')->get();

        $this->view = $view = view('livewire.produto.modal-cadastrar-produto', ['estoques' => $estoques, 'categorias'=>  $this->categorias])->render();
    }

    public function render()
    {
        return view('livewire.produto.cadastrar-produto-card');
    }
}
