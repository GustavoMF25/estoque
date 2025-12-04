<?php

namespace App\Livewire\Carrinho;

use App\Models\Cliente;
use App\Models\Venda;
use App\Models\VendaItem;
use App\Models\Produto;
use App\Models\ProdutosUnidades;
use App\Models\ProdutoVinculos;
use App\Services\ProdutoUnidadeService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ConfirmarVenda extends Component
{
    public $protocolo = '';
    public $cliente_id = '';
    public $enderecoSelecionado = '';
    public $total_original;
    public $total_final;
    public $desconto_percentual;
    public $editando_total = false;
    public $carrinho;
    public $clientes;
    public $disponiveis;


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

        $carrinho[$produtoId]['quantidade'] += 1;

        $this->carrinho = $carrinho;
        session(['carrinho' => $carrinho]);
        $this->total_original = $this->calcularTotal();

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

            $this->carrinho = $carrinho;
            session(['carrinho' => $carrinho]);


            $this->total_original = $this->calcularTotal();
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

            $venda = Venda::create([
                'loja_id' => null,
                'user_id' => auth()->id(),
                'cliente_id' => $this->cliente_id ?: null,
                'protocolo' => $this->protocolo,
                'valor_total' => $this->total_original,
                'desconto' => $this->desconto_percentual ?: null,
                'valor_final' => $this->total_final,
                'status' => 'aberta',
                'empresa_id' => auth()->user()->empresa_id,
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

                ProdutoUnidadeService::alterarStatus(
                    $unidadesDisponiveis,
                    'vendido',
                    ProdutoUnidadeService::tipoMovimentacaoPorStatus('vendido'),
                    "Venda ID: {$venda->id} - Unidades: " . $unidadesDisponiveis->pluck('codigo_unico')->implode(', ')
                );
            }

            session()->forget('carrinho');
            $this->dispatch('atualizarCarrinho');

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => $this->desconto_percentual  > 0
                    ? "Venda concluída com desconto de " . $this->desconto_percentual. " %."
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

    public function updatedDescontoPercentual($value)
    {
        $value = floatval($value);

        if ($value < 0) $value = 0;
        if ($value > 100) $value = 100;

        $this->desconto_percentual = $value;
        $this->editando_total = false;

        $desconto = ($this->total_original * $value) / 100;
        $this->total_final = max($this->total_original - $desconto, 0);
    }

    public function updatedTotalOriginal($value)
    {
        // Ignora valores vazios
        if ($value === "" || $value === null) {
            return;
        }

        // Agora sim converte
        $valor = str_replace(',', '.', $value);

        if (!is_numeric($valor)) {
            return; // evita Livewire resetar
        }

        $valor = floatval($valor);

        // Impede negativo
        if ($valor < 0) {
            $valor = 0;
        }

        $this->editando_total = true;
        $this->total_final = $valor;
    }

    private function calcularTotal(): float
    {
        if (empty($this->carrinho)) {
            return 0;
        }


        // $itens é aquele array que você usa na view: ['produto_id', 'nome', 'quantidade', 'preco_unitario', ...]
        $valor =  collect($this->carrinho)->sum(function ($item) {
            $quantidade = $item['quantidade'] ?? 0;
            $preco = $item['preco_unitario'] ?? 0;

            return $quantidade * $preco;
        });


        $this->total_final = $valor;
        return $valor;
    }


    public function mount()
    {
        $this->carrinho = session('carrinho', []);
        $this->clientes = Cliente::orderBy('nome')->get();
        $this->disponiveis = [];

        foreach ($this->carrinho as $item) {

            $produto = Produto::findOrFail($item['produto_id']);

            $this->disponiveis[$item['produto_id']] = $produto ? $produto->Disponiveis : 0;

            $this->total_original = $this->total_original + ($item['quantidade'] * $item['preco_unitario']);
        }
        $this->total_final =  $this->total_original;
    }

    public function render()
    {
        return view('livewire.carrinho.confirmar-venda', [
            'itens' => $this->carrinho,
            'clientes' => $this->clientes,
            'total' => collect($this->carrinho)->sum(fn($item) => $item['quantidade'] * $item['preco_unitario']),
            'disponiveis' => $this->disponiveis,
        ]);
    }
}
