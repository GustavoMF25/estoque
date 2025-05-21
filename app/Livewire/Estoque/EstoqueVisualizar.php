<?php

namespace App\Livewire\Estoque;

use App\Models\Estoque;
use Livewire\Component;

class EstoqueVisualizar extends Component
{
    public Estoque $estoque;

    public function mount($estoqueId)
    {
        $this->estoque = Estoque::withTrashed()->withCount('produtos')->find($estoqueId);
    }

    public function render()
    {
        return view('livewire.estoque.estoque-visualizar');
    }
}
