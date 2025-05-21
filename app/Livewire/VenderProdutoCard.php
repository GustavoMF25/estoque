<?php

namespace App\Livewire;

use App\Models\Estoque;
use App\Models\Produto;
use Livewire\Component;

class VenderProdutoCard extends Component
{
    public int $quantidade;
    public string $view;

    public function mount()
    {
        $this->quantidade = Produto::whereHas('ultimaMovimentacao', function ($query) {
            $query->where('tipo', 'saida');
        })->count();

        $produtosAgrupados = Produto::select('nome')
            ->whereHas('ultimaMovimentacao', function ($query) {
                $query->where('tipo', 'disponivel');
            })
            ->selectRaw('COUNT(*) as total')
            ->groupBy('nome')
            ->get();

        $this->view = $view = view('produto.modal-cadastrar-venda', ['produtos' => $produtosAgrupados])->render();
    }
    public function render()
    {
        return view('livewire.vender-produto-card');
    }
}
