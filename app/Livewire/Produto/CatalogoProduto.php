<?php

namespace App\Livewire\Produto;


use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Produto;
use App\Services\VendaEstoqueService;

class CatalogoProduto extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 8;
    public array $quantidades = [];
    public array $chegadasAbertasAbertas = [];


    protected $queryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function toggleChegadas(int $produtoId): void
    {
        $aberto = (bool) ($this->chegadasAbertasAbertas[$produtoId] ?? false);
        $this->chegadasAbertasAbertas[$produtoId] = ! $aberto;
    }

    public function adicionarCarrinho($produtoId)
    {
        // 🔍 Busca o produto base
        $produto = Produto::query()->Ativo()->findOrFail($produtoId);

        $disponibilidade = VendaEstoqueService::disponibilidadeAtual($produto);
        $quantidadeDisponivel = $disponibilidade['total'];
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
                'preco_unitario' => $produto->valor_venda ?? $produto->preco ?? 0,
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
            ->with([
                'chegadasAbertas' => fn($q) => $q
                    ->select([
                        'id',
                        'produto_id',
                        'quantidade_total',
                        'quantidade_comprometida',
                        'previsao_chegada',
                    ])
                    ->whereColumn('quantidade_comprometida', '<', 'quantidade_total')
                    ->orderByRaw('previsao_chegada IS NULL')
                    ->orderBy('previsao_chegada'),
            ])
            ->withCount([
                'unidades as disponiveis_count' => fn($q) => $q->where('status', 'disponivel'),
            ])
            ->withSum([
                'chegadasAbertas as a_chegar_total' => fn($q) => $q,
            ], 'quantidade_total')
            ->withSum([
                'chegadasAbertas as a_chegar_comprometida_total' => fn($q) => $q,
            ], 'quantidade_comprometida')
            ->Ativo()
            ->where('nome', 'like', '%' . $this->search . '%')
            ->where(function ($query) {
                $query->whereHas('unidades', fn($q) => $q->where('status', 'disponivel'))
                    ->orWhereHas('chegadasAbertas', fn($q) => $q->whereColumn('quantidade_comprometida', '<', 'quantidade_total'));
            })
            ->paginate($this->perPage);

        $products->getCollection()->transform(function ($product) {
            $aChegarDisponivel = max(
                0,
                (int) ($product->a_chegar_total ?? 0) - (int) ($product->a_chegar_comprometida_total ?? 0)
            );

            $product->chegadas_abertas_catalogo = $product->chegadasAbertas
                ->map(function ($chegada) {
                    return [
                        'quantidade_disponivel' => (int) $chegada->quantidade_disponivel,
                        'previsao_chegada' => $chegada->previsao_chegada,
                    ];
                })
                ->filter(fn($chegada) => $chegada['quantidade_disponivel'] > 0)
                ->values();

            $product->a_chegar_disponivel_count = $aChegarDisponivel;
            $product->disponivel_para_venda_count = (int) $product->disponiveis_count + $aChegarDisponivel;

            return $product;
        });

        return view('livewire.produto.catalogo-produto', [
            'products' => $products,
        ]);
    }
}
