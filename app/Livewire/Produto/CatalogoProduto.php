<?php

namespace App\Livewire\Produto;


use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Produto;
use App\Models\ProdutosAgrupados;
use App\Models\ProdutosUnidades;

class CatalogoProduto extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 8;
    public array $quantidades = [];


    protected $queryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function adicionarCarrinho($produtoId)
    {
        // 🔍 Busca o produto base
        $produto = Produto::findOrFail($produtoId);

        $quantidadeDisponivel = ProdutosUnidades::where('produto_id', $produto->id)
            ->where('status', 'disponivel')
            ->count();
        $quantidadeSolicitada = $this->quantidades[$produto->id] ?? 1;

        $carrinho = session('carrinho', []);

        $quantidadeNoCarrinho = $carrinho[$produto->id]['quantidade'] ?? 0;

        $quantidadeRestante = $quantidadeDisponivel - $quantidadeNoCarrinho;

        if ($quantidadeSolicitada > $quantidadeRestante) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => "Já existem {$quantidadeNoCarrinho}x '{$produto->nome}' no carrinho. Máximo permitido: {$quantidadeDisponivel}."
            ]);
            return;
        }

        if (isset($carrinho[$produto->id])) {
            $carrinho[$produto->id]['quantidade'] += $quantidadeSolicitada;
        } else {
            $carrinho[$produto->id] = [
                'produto_id' => $produto->id,
                'nome' => $produto->nome,
                'quantidade' => $quantidadeSolicitada,
                'preco_unitario' => $produto->preco ?? 0,
                'imagem' => $produto->imagem ?? null,
                'codigo_barras' => $produto->codigo_barras ?? null,
            ];
        }

        session(['carrinho' => $carrinho]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => "{$quantidadeSolicitada}x '{$produto->nome}' adicionados ao carrinho."
        ]);

        $this->dispatch('atualizarCarrinho');
    }


    public function render()
    {
        $products = Produto::query()
            ->withCount([
                'unidades as disponiveis_count' => fn($q) => $q->where('status', 'disponivel'),
            ])
            ->where('nome', 'like', '%' . $this->search . '%')
            ->whereHas('unidades', fn($q) => $q->where('status', 'disponivel'))
            ->paginate($this->perPage);

        return view('livewire.produto.catalogo-produto', [
            'products' => $products,
        ]);
    }
}
