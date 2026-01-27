<?php

namespace App\Livewire\Vendas;

use App\Models\Venda;
use Livewire\Component;

class VisualizarVenda extends Component
{
    public $id;
    public $venda;

    public function mount($id)
    {
        $this->id = $id;
        $this->venda = Venda::with(['itens.produto', 'usuario', 'loja', 'cliente.enderecoPadrao'])->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.vendas.visualizar-venda');
    }
}
