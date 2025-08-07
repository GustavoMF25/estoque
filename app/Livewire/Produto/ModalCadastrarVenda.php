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
    public $buscaProduto;
    public $qtdMax;
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

    public function BuscaProduto()
    {
        if (strlen($this->buscaProduto) > 1) {
            $this->produtos = Produto::whereHas('ultimaMovimentacao', function ($q) {
                $q->where('tipo', 'disponivel');
            })
                ->when($this->categoriaId, fn($q) => $q->where('categoria_id', $this->categoriaId))
                ->where('nome', 'like', '%' . $this->buscaProduto . '%')
                ->select('nome')
                ->selectRaw('COUNT(*) as total')
                ->groupBy('nome')
                ->limit(10)
                ->get();
        } else {
            $this->carregarProdutos();
        }
    }

    public function carregarQuantidade()
    {
        $query = Produto::query()
            ->select('nome')
            ->whereHas('ultimaMovimentacao', function ($q) {
                $q->where('tipo', 'disponivel');
            });

        if (!empty($this->categoriaId)) {
            $query->where('categoria_id', $this->categoriaId);
        }
        if (!empty($this->produtoSelecionado)) {
            $query->where('nome', $this->produtoSelecionado);
        }

        $query = $query
            ->selectRaw('COUNT(*) as total')
            ->groupBy('nome')
            ->get()
            ->toArray();

        $this->qtdMax = $query[0]['total'];
        $this->carregarProdutos();
        $this->quantidade = 1;
        $this->resetErrorBag();
    }

    public function verificaQuantidade()
    {
        if ($this->quantidade > $this->qtdMax) {
            $this->quantidade = $this->qtdMax;
            $this->addError('quantidade', "A quantidade não pode ser maior que {$this->qtdMax}.");
        } else {
            $this->resetErrorBag('quantidade');
        }
        $this->carregarProdutos();
    }

    public function render()
    {
        return view('livewire.produto.modal-cadastrar-venda');
    }
}
