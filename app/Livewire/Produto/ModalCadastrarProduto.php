<?php

namespace App\Livewire\Produto;

use App\Models\Categoria;
use App\Models\Estoque;
use App\Models\Produto;
use Livewire\Component;

class ModalCadastrarProduto extends Component
{

    public int $quantidade;
    public $categorias;
    public $estoques;

    public function mount()
    {
        $this->quantidade = Produto::whereHas('ultimaMovimentacao', function ($query) {
            $query->where('tipo', 'disponivel');
        })->count();
        
        $this->estoques = Estoque::all();
        $this->categorias = Categoria::where('ativo', true)->orderBy('nome')->get();
    }
    public function render()
    {
        return view('livewire.produto.modal-cadastrar-produto');
    }
}
