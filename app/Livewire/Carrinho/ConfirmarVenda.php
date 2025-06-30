<?php

namespace App\Livewire\Carrinho;

use App\Models\Venda;
use App\Models\VendaItem;
use App\Models\Produto;
use App\Models\Movimentacao;
use App\Services\MovimentacaoService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ConfirmarVenda extends Component
{
    public $protocolo = '';

    protected $rules = [
        'protocolo' => 'required|string|max:255',
    ];

    public function aumentarQuantidade($produtoNome)
    {
        $agrupado = \App\Models\ProdutosAgrupados::where('nome', $produtoNome)
            ->where('ultima_movimentacao', 'disponivel')
            ->firstOrFail();

        $carrinho = session('carrinho', []);
        $quantidadeNoCarrinho = $carrinho[$produtoNome]['quantidade'] ?? 0;
        $quantidadeDisponivel = $agrupado->quantidade_produtos;

        if ($quantidadeNoCarrinho + 1 > $quantidadeDisponivel) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => "Já existem {$quantidadeNoCarrinho}x '{$produtoNome}' no carrinho. Máximo permitido: {$quantidadeDisponivel}."
            ]);
            return;
        }

        if (isset($carrinho[$produtoNome])) {
            $carrinho[$produtoNome]['quantidade'] += 1;
        } else {
            $carrinho[$produtoNome] = [
                'nome' => $produtoNome,
                'quantidade' => 1,
                'preco_unitario' => $agrupado->preco ?? 0,
                'imagem' => $agrupado->imagem ?? null,
                'codigo_barras' => $agrupado->codigo_barras ?? null,
            ];
        }

        session(['carrinho' => $carrinho]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => "Item '{$produtoNome}' incrementado no carrinho."
        ]);

        $this->dispatch('atualizarCarrinho');
    }
    public function diminuirQuantidade($produtoNome)
    {
        $carrinho = session('carrinho', []);
        if (isset($carrinho[$produtoNome])) {
            if ($carrinho[$produtoNome]['quantidade'] > 1) {
                $carrinho[$produtoNome]['quantidade'] -= 1;
            } else {
                unset($carrinho[$produtoNome]);
            }
            session(['carrinho' => $carrinho]);

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => "Quantidade de '{$produtoNome}' reduzida no carrinho."
            ]);

            $this->dispatch('atualizarCarrinho');
        }
    }

    public function confirmar()
    {
        $this->validate();

        $carrinho = session('carrinho', []);

        if (empty($carrinho)) {
            return $this->dispatch('toast', ['type' => 'error', 'message' => 'Carrinho está vazio.']);
        }

        // DB::beginTransaction();
        try {
            $venda = Venda::create([
                'empresa_id' => 1,
                'loja_id' => null,
                'user_id' => auth()->id(),
                'protocolo' => $this->protocolo,
                'valor_total' => collect($carrinho)->sum(function ($item) {
                    return $item['quantidade'] * $item['preco_unitario'];
                }),
                'status' => 'aberta',
            ]);

            foreach ($carrinho as $item) {
                for ($i = 0; $i < $item['quantidade']; $i++) {

                    // Buscar o ID do produto pelo nome
                    $produto = Produto::where('nome', $item['nome'])->whereHas('ultimaMovimentacao', function ($q) {
                        $q->where('tipo', 'disponivel');
                    })->firstOrFail();


                    VendaItem::create([
                        'venda_id' => $venda->id,
                        'produto_id' => $produto->id,
                        'valor_unitario' => $item['preco_unitario'],
                        'valor_total' => $item['preco_unitario'],
                    ]);


                    MovimentacaoService::registrar([
                        'produto_id' => $produto->id,
                        'quantidade' => 1,
                        'tipo' => 'saida',
                        'observacao' => 'Venda ID: ' . $venda->id,
                    ]);
                }
            }

            // DB::commit();

            session()->forget('carrinho');
            $this->dispatch('atualizarCarrinho');
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Venda realizada com sucesso!']);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Erro ao realizar venda: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        $carrinho = session('carrinho', []);
        $disponiveis = [];

        foreach ($carrinho as $item) {
            $agrupado = \App\Models\ProdutosAgrupados::where('nome', $item['nome'])
                ->where('ultima_movimentacao', 'disponivel')
                ->first();

            $disponiveis[$item['nome']] = $agrupado ? $agrupado->quantidade_produtos : 0;
        }

        return view('livewire.carrinho.confirmar-venda', [
            'itens' => $carrinho,
            'total' => collect($carrinho)->sum(fn($item) => $item['quantidade'] * $item['preco_unitario']),
            'disponiveis' => $disponiveis,
        ]);
    }
}
