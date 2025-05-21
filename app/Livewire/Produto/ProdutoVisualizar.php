<?php

namespace App\Livewire\Produto;

use App\Models\Produto;
use Livewire\Component;

class ProdutoVisualizar extends Component
{
    public Produto $produto;

    public function mount($produtoId)
    {
        $this->produto = Produto::with(['estoque', 'movimentacoes'])->withTrashed()->findOrFail($produtoId);
    }

    public function render()
    {
        return view('livewire.produto.produto-visualizar');
    }
}
