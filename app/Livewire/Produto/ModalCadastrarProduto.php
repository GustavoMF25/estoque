<?php

namespace App\Livewire\Produto;

use App\Models\Categoria;
use App\Models\Estoque;
use App\Models\Fabricante;
use App\Models\Produto;
use Livewire\Component;

class ModalCadastrarProduto extends Component
{

    public int $quantidade;
    public $categorias;
    public $estoques;
    public $fabricantes;

    public function mount()
    {
        $this->quantidade = Produto::whereHas('ultimaMovimentacao', function ($query) {
            $query->where('tipo', 'disponivel');
        })->count();
        
        $this->estoques = Estoque::all();
        $this->categorias = Categoria::where('ativo', true)->orderBy('nome')->get();
        $this->fabricantes = Fabricante::orderBy('nome')->get();
    }
    public function render()
    {
        return view('livewire.produto.modal-cadastrar-produto');
    }
}
