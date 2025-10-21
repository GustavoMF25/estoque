<?php

namespace App\Livewire\Carrinho;

use App\Models\Cliente;
use App\Models\Venda;
use App\Models\VendaItem;
use App\Models\Produto;
use App\Models\Movimentacao;
use App\Models\ProdutosUnidades;
use App\Models\ProdutoVinculos;
use App\Services\MovimentacaoService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ConfirmarVenda extends Component
{
    public $protocolo = '';
    public $cliente_id = '';
    public $enderecoSelecionado = '';

    protected $rules = [
        'protocolo' => 'required|string|max:255',
        'cliente_id' => 'required|exists:clientes,id',
    ];

    public function aumentarQuantidade($produtoId, $nome)
    {
        $produto = Produto::find($produtoId);

        $carrinho = session('carrinho', []);
        $quantidadeNoCarrinho = $carrinho[$produtoId]['quantidade'] ?? 0;
        $quantidadeDisponivel = $produto->Disponiveis;

        if ($quantidadeNoCarrinho + 1 > $quantidadeDisponivel) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => "Já existem {$quantidadeNoCarrinho}x '{$nome}' no carrinho. Máximo permitido: {$quantidadeDisponivel}."
            ]);
            return;
        }

        if (isset($carrinho[$produtoId])) {
            $carrinho[$produtoId]['quantidade'] += 1;
        } else {
            $carrinho[$produto->id] = [
                'produto_id' => $produto->id,
                'nome' => $produto->nome,
                'quantidade' => 1,
                'preco_unitario' => $produto->preco ?? 0,
                'imagem' => $produto->imagem ?? null,
                'codigo_barras' => $produto->codigo_barras ?? null,
            ];
        }

        session(['carrinho' => $carrinho]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => "Item '{$nome}' incrementado no carrinho."
        ]);

        $this->dispatch('atualizarCarrinho');
    }

    public function diminuirQuantidade($produtoId, $nome)
    {
        $carrinho = session('carrinho', []);
        if (isset($carrinho[$produtoId])) {
            if ($carrinho[$produtoId]['quantidade'] > 1) {
                $carrinho[$produtoId]['quantidade'] -= 1;
            } else {
                unset($carrinho[$produtoId]);
            }
            session(['carrinho' => $carrinho]);

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => "Quantidade de '{$nome}' reduzida no carrinho."
            ]);

            $this->dispatch('atualizarCarrinho');
        }
    }

    private function calcularDescontoCombo($carrinho)
    {
        $descontoTotal = 0;

        // percorre todos os produtos do carrinho
        foreach ($carrinho as $itemNome => $item) {
            $produtoPrincipal = Produto::where('nome', $itemNome)->first();

            if (!$produtoPrincipal) continue;

            $vinculos = ProdutoVinculos::where('produto_principal_id', $produtoPrincipal->id)->get();

            foreach ($vinculos as $vinculo) {
                // pega o nome do produto vinculado
                $produtoVinculado = Produto::find($vinculo->produto_vinculado_id);
                if (!$produtoVinculado) continue;

                // verifica se o combo está completo
                $temPrincipal = isset($carrinho[$itemNome]);
                $temVinculado = isset($carrinho[$produtoVinculado->nome]) && $carrinho[$produtoVinculado->nome]['quantidade'] >= $vinculo->quantidade;

                if ($temPrincipal && $temVinculado) {
                    $subtotalCombo = ($carrinho[$itemNome]['quantidade'] * $carrinho[$itemNome]['preco_unitario']) + ($carrinho[$produtoVinculado->nome]['quantidade'] * $carrinho[$produtoVinculado->nome]['preco_unitario']);

                    $desconto = ($subtotalCombo * $vinculo->desconto_combo) / 100;
                    $descontoTotal += $desconto;
                }
            }
        }

        return $descontoTotal;
    }

    public function confirmar()
    {
        // $this->validate();
        $carrinho = session('carrinho', []);

        if (empty($carrinho)) {
            return $this->dispatch('toast', ['type' => 'error', 'message' => 'Carrinho está vazio.']);
        }

        try {
            // calcula o valor total bruto
            $valorTotal = collect($carrinho)->sum(fn($item) => $item['quantidade'] * $item['preco_unitario']);

            // aplica o desconto combo
            $descontoCombo = $this->calcularDescontoCombo($carrinho);
            $valorFinal = $valorTotal - $descontoCombo;

            $venda = Venda::create([
                'empresa_id' => 1,
                'loja_id' => null,
                'user_id' => auth()->id(),
                'cliente_id' => $this->cliente_id ?: null,
                'protocolo' => $this->protocolo,
                'valor_total' => $valorTotal,
                'desconto' => $descontoCombo,
                'valor_final' => $valorFinal,
                'status' => 'aberta',
            ]);

            foreach ($carrinho as $item) {
                $produto = Produto::findOrFail($item['produto_id']);

                $unidadesDisponiveis = ProdutosUnidades::where('produto_id', $produto->id)
                    ->where('status', 'disponivel')
                    ->limit($item['quantidade'])
                    ->get();

                if ($unidadesDisponiveis->count() < $item['quantidade']) {
                    throw new \Exception("O produto '{$produto->nome}' possui apenas {$unidadesDisponiveis->count()} unidades disponíveis.");
                }

                $vendaItem = VendaItem::create([
                    'venda_id' => $venda->id,
                    'produto_id' => $produto->id,
                    'quantidade' => $item['quantidade'],
                    'valor_unitario' => $item['preco_unitario'],
                    'valor_total' => $item['preco_unitario'] * $item['quantidade'],
                ]);

                $vendaItem->unidades()->attach($unidadesDisponiveis->pluck('id')->toArray());

                foreach ($unidadesDisponiveis as $unidade) {
                    $unidade->update(['status' => 'vendido']);

                    MovimentacaoService::registrar([
                        'produto_id' => $produto->id,
                        'quantidade' => 1,
                        'tipo' => 'saida',
                        'observacao' => "Venda ID: {$venda->id} - Unidade {$unidade->codigo_unico}",
                    ]);
                }
            }

            session()->forget('carrinho');
            $this->dispatch('atualizarCarrinho');

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => $descontoCombo > 0
                    ? "Venda concluída com desconto de R$ " . number_format($descontoCombo, 2, ',', '.')
                    : 'Venda realizada com sucesso!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Erro ao realizar venda: ' . $e->getMessage()]);
        }
    }


    public function updatedClienteId($value)
    {
        $cliente = \App\Models\Cliente::with('enderecoPadrao')->find($value);
        $this->enderecoSelecionado = $cliente?->enderecoPadrao
            ? "{$cliente->enderecoPadrao->rua}, {$cliente->enderecoPadrao->numero} - {$cliente->enderecoPadrao->cidade}/{$cliente->enderecoPadrao->estado}"
            : '';
    }

    public function render()
    {
        $carrinho = session('carrinho', []);
        $clientes = Cliente::orderBy('nome')->get(); // 👈 lista de clientes
        $disponiveis = [];

        foreach ($carrinho as $item) {

            $produto = Produto::findOrFail($item['produto_id']);

            $disponiveis[$item['produto_id']] = $produto ? $produto->Disponiveis : 0;
        }

        return view('livewire.carrinho.confirmar-venda', [
            'itens' => $carrinho,
            'clientes' => $clientes,
            'total' => collect($carrinho)->sum(fn($item) => $item['quantidade'] * $item['preco_unitario']),
            'disponiveis' => $disponiveis,
        ]);
    }
}
