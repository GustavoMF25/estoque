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
        $q = Produto::query()
            ->Ativo()
            ->whereHas('ultimaMovimentacao', fn($x) => $x->where('tipo', 'disponivel'))
            ->when($this->categoriaId, fn($x) => $x->where('categoria_id', $this->categoriaId));

        if (strlen((string)$this->buscaProduto) > 1) {
            $q->where('nome', 'like', '%' . $this->buscaProduto . '%');
        }

        $lista = $q->selectRaw('nome, COUNT(*) as total')
            ->groupBy('nome')
            ->orderBy('nome')
            ->get();

        $this->produtos = $lista;
    }

    public function ProdutoSelecionado()
    {
        if (!$this->produtoSelecionado) {
            $this->qtdMax = 0;
            $this->quantidade = 1;
            return;
        }

        $this->qtdMax = Produto::query()
            ->Ativo()
            ->whereHas('ultimaMovimentacao', fn($q) => $q->where('tipo', 'disponivel'))
            ->when($this->categoriaId, fn($q) => $q->where('categoria_id', $this->categoriaId))
            ->where('nome', $this->produtoSelecionado)
            ->count(); // evita acessar índice [0]

        $this->quantidade = 1;
        $this->resetErrorBag('quantidade');

        // $this->carregarProdutos();
    }

    // wire:change="carregarQuantidade"
    public function carregarQuantidade()
    {
        $query = Produto::query()
            ->Ativo()
            ->whereHas('ultimaMovimentacao', fn($q) => $q->where('tipo', 'disponivel'));

        if (!empty($this->categoriaId)) {
            $query->where('categoria_id', $this->categoriaId);
        }
        if (!empty($this->produtoSelecionado)) {
            $query->where('nome', $this->produtoSelecionado);
        }

        $row = $query
            ->selectRaw('nome, COUNT(*) as total')
            ->groupBy('nome')
            ->first();

        $this->qtdMax = $row->total ?? 0;

        // NÃO recarrega a lista aqui — mantém o selected estável
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
