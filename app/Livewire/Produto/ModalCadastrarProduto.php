<?php

namespace App\Livewire\Produto;

use App\Models\Estoque;
use App\Models\Produto;
use Livewire\Component;

class ModalCadastrarProduto extends Component
{

    public int $quantidade;
    public $estoques;

    public function mount()
    {
        $this->quantidade = Produto::whereHas('ultimaMovimentacao', function ($query) {
            $query->where('tipo', 'disponivel');
        })->count();
        
        $this->estoques = Estoque::all();
    }
    public function render()
    {
        return view('livewire.produto.modal-cadastrar-produto');
    }
}
