<?php

namespace App\Livewire\Produto;


use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Produto;
use App\Models\ProdutosAgrupados;

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

    public function adicionarCarrinho($produtoNome)
    {
        $agrupado = ProdutosAgrupados::where('nome', $produtoNome)
         ->where('ultima_movimentacao', 'disponivel')->firstOrFail();
        $quantidadeSolicitada = $this->quantidades[$produtoNome] ?? 1;

        $carrinho = session('carrinho', []);

        $quantidadeNoCarrinho = $carrinho[$produtoNome]['quantidade'] ?? 0;

        $quantidadeDisponivel = $agrupado->quantidade_produtos;
        $quantidadeRestante = $quantidadeDisponivel - $quantidadeNoCarrinho;

        if ($quantidadeSolicitada > $quantidadeRestante) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => "Já existem {$quantidadeNoCarrinho}x '{$produtoNome}' no carrinho. Máximo permitido: {$quantidadeDisponivel}."
            ]);
            session()->flash('error',);
            return;
        }

        // Atualiza entrada única baseada no nome
        if (isset($carrinho[$produtoNome])) {
            $carrinho[$produtoNome]['quantidade'] += $quantidadeSolicitada;
        } else {
            $carrinho[$produtoNome] = [
                'nome' => $produtoNome,
                'quantidade' => $quantidadeSolicitada,
                'preco_unitario' => $agrupado->preco ?? 0,
                'imagem' => $agrupado->imagem ?? null,
                'codigo_barras' => $agrupado->codigo_barras ?? null,
            ];
        }

        session()->put('carrinho', $carrinho);
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => "{$quantidadeSolicitada}x {$produtoNome} adicionados ao carrinho."
        ]);

        $this->dispatch('atualizarCarrinho');
    }


    public function render()
    {
        $products = ProdutosAgrupados::query()
            ->where('nome', 'like', '%' . $this->search . '%')
            ->where('ultima_movimentacao', 'disponivel')
            // ->withQueryString()
            ->paginate($this->perPage);

        return view('livewire.produto.catalogo-produto', [
            'products' => $products,
        ]);
    }
}
