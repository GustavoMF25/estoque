<?php

namespace App\Livewire\Produto;

use Livewire\Component;
use App\Models\Categoria;
use App\Models\Produto;

class ModalCadastrarVenda extends Component
{
    public $quantidade = 1;
    public $categoriaId;
    public $produtoSelecionado;
    public $categorias = [];
    public $produtos = [];

    public function mount()
    {
        $this->categorias = Categoria::where('ativo', true)->orderBy('nome')->get();
        $this->carregarProdutos();
    }

    public function categoriaSelecionada()
    {
        $this->carregarProdutos();
    }

    private function carregarProdutos()
    {
        $query = Produto::query()
            ->select('nome')
            ->whereHas('ultimaMovimentacao', function ($q) {
                $q->where('tipo', 'disponivel');
            });

        if (!empty($this->categoriaId)) {
            $query->where('categoria_id', $this->categoriaId);
        }

        $this->produtos = $query
            ->selectRaw('COUNT(*) as total')
            ->groupBy('nome')
            ->get();
    }

    public function venderProduto()
    {
        session()->flash('success', 'Venda registrada com sucesso!');
    }

    public function render()
    {
        return view('livewire.produto.modal-cadastrar-venda');
    }
}
