<?php

namespace App\Livewire;

use App\Models\Estoque;
use App\Models\Produto;
use Livewire\Component;



class CadastrarProdutoCard extends Component
{

    public int $quantidade;
    public string $view;

    public function mount()
    {
        $this->quantidade = Produto::whereHas('ultimaMovimentacao', function ($query) {
            $query->where('tipo', 'disponivel');
        })->count();
        $estoques = Estoque::all();
        $this->view = $view = view('produto.modal-cadastrar-produto', ['estoques' => $estoques])->render();
    }

    public function render()
    {
        return view('livewire.cadastrar-produto-card', [
            'quantidade' => $this->quantidade,
            'view' => $this->view, // passa também para o Blade
        ]);
    }
}
