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
    public $perPage = 12;

    protected $queryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage(); // mantém a paginação correta ao digitar
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
