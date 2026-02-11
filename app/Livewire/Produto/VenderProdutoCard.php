<?php

namespace App\Livewire\Produto;

use App\Models\Categoria;
use App\Models\Estoque;
use App\Models\Produto;
use Livewire\Component;

class VenderProdutoCard extends Component
{

    // INATIVADO
    public int $quantidade;
    public string $view;
    public $produtos = [];
    public $categorias = [];

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

        $this->categorias = Categoria::where('ativo', true)->orderBy('nome')->get();
        $this->view = view('livewire.produto.modal-cadastrar-venda', ['produtos' => $produtosAgrupados, 'categorias' => $this->categorias])->render();
    }

    public function categoriaSelecionada($value)
    {
        $this->carregarProdutos();
    }

    public function render()
    {
        return view('livewire.produto.vender-produto-card');
    }
}
