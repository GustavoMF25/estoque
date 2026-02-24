<?php

namespace App\Livewire\Carrinho;

use App\Models\Cliente;
use App\Models\Categoria;
use App\Models\Loja;
use App\Models\Notificacao;
use App\Models\Produto;
use App\Models\ProdutoVinculos;
use App\Models\User;
use App\Models\Venda;
use App\Models\VendaItem;
use App\Services\VendaEstoqueService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ConfirmarVenda extends Component
{
    public $protocolo = '';
    public $cliente_id = '';
    public $enderecoSelecionado = '';
    public $loja_id = '';
    public $usuario_tem_loja_vinculada = false;
    public $nome_loja_vinculada = '';
    public $frete = 0;
    public $valor_venda = null;
    public $valor_venda_editado = false;
    public $desconto_percentual = 0;
    public $forma_pagamento = '';
    public $status_pagamento = '';
    public $parcelas_cartao = '';
    public $preco_unitario = [];
    public $atualizar_preco_base = [];

    protected $rules = [
        'protocolo' => 'required|string|max:255',
        'cliente_id' => 'required|exists:clientes,id',
        'loja_id' => 'required|exists:lojas,id',
        'frete' => 'nullable|numeric|min:0',
        'valor_venda' => 'nullable|numeric|min:0',
        'desconto_percentual' => 'nullable|numeric|min:0|max:100',
        'forma_pagamento' => 'nullable|string|max:50',
        'status_pagamento' => 'nullable|string|max:50',
        'parcelas_cartao' => 'nullable|integer|min:1|max:24',
    ];

    public function mount()
    {
        $usuario = auth()->user();
        $empresaId = auth()->user()->empresa_id ?? 1;
        $lojas = Loja::where('empresa_id', $empresaId)->orderBy('nome')->get();

        $lojaVinculada = null;
        if (!empty($usuario->loja_id)) {
            $lojaVinculada = $lojas->firstWhere('id', $usuario->loja_id);
        }

        if ($lojaVinculada) {
            $this->loja_id = (string) $lojaVinculada->id;
            $this->usuario_tem_loja_vinculada = true;
            $this->nome_loja_vinculada = (string) $lojaVinculada->nome;
        } else {
            $this->usuario_tem_loja_vinculada = false;
            $this->nome_loja_vinculada = '';
            if ($lojas->count() === 1) {
                $this->loja_id = (string) $lojas->first()->id;
            }
        }

        $this->sincronizarPrecosComCarrinho();
        $this->sincronizarValorVendaComCarrinho();
    }

    public function aumentarQuantidade($produtoId, $nome)
    {
        $produto = Produto::find($produtoId);

        $carrinho = session('carrinho', []);
        $quantidadeNoCarrinho = $carrinho[$produtoId]['quantidade'] ?? 0;
        $disponibilidade = VendaEstoqueService::disponibilidadeAtual($produto);
        $quantidadeDisponivel = $disponibilidade['total'];

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
                'preco_unitario' => $produto->valor_venda ?? $produto->preco ?? 0,
                'imagem' => $produto->imagem ?? null,
                'codigo_barras' => $produto->codigo_barras ?? null,
            ];
        }

        session(['carrinho' => $carrinho]);
        $this->sincronizarPrecosComCarrinho();
        $this->sincronizarValorVendaComCarrinho();

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
            $this->sincronizarPrecosComCarrinho();
            $this->sincronizarValorVendaComCarrinho();

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
        $carrinhoPorNome = collect($carrinho)->keyBy('nome');

        // percorre todos os produtos do carrinho
        foreach ($carrinho as $item) {
            $itemNome = $item['nome'] ?? null;
            if (!$itemNome) {
                continue;
            }

            $produtoPrincipal = Produto::where('nome', $itemNome)->first();

            if (!$produtoPrincipal) {
                continue;
            }

            $vinculos = ProdutoVinculos::where('produto_principal_id', $produtoPrincipal->id)->get();

            foreach ($vinculos as $vinculo) {
                // pega o nome do produto vinculado
                $produtoVinculado = Produto::find($vinculo->produto_vinculado_id);
                if (!$produtoVinculado) {
                    continue;
                }

                // verifica se o combo está completo
                $itemPrincipalCarrinho = $carrinhoPorNome->get($itemNome);
                $itemVinculadoCarrinho = $carrinhoPorNome->get($produtoVinculado->nome);
                $temPrincipal = !empty($itemPrincipalCarrinho);
                $temVinculado = !empty($itemVinculadoCarrinho) && $itemVinculadoCarrinho['quantidade'] >= $vinculo->quantidade;

                if ($temPrincipal && $temVinculado) {
                    $subtotalCombo = ($itemPrincipalCarrinho['quantidade'] * $itemPrincipalCarrinho['preco_unitario'])
                        + ($itemVinculadoCarrinho['quantidade'] * $itemVinculadoCarrinho['preco_unitario']);

                    $desconto = ($subtotalCombo * $vinculo->desconto_combo) / 100;
                    $descontoTotal += $desconto;
                }
            }
        }

        return $descontoTotal;
    }

    private function totalItensCarrinho(array $carrinho): float
    {
        return (float) collect($carrinho)->sum(fn($item) => ((float) $item['quantidade']) * ((float) $item['preco_unitario']));
    }

    private function sincronizarPrecosComCarrinho(): void
    {
        $carrinho = session('carrinho', []);
        $idsCarrinho = collect($carrinho)->keys()->map(fn($id) => (string) $id)->all();

        foreach ($carrinho as $produtoId => $item) {
            $key = (string) $produtoId;
            if (!array_key_exists($key, $this->preco_unitario)) {
                $this->preco_unitario[$key] = (float) ($item['preco_unitario'] ?? 0);
            }
            if (!array_key_exists($key, $this->atualizar_preco_base)) {
                $this->atualizar_preco_base[$key] = false;
            }
        }

        foreach (array_keys($this->preco_unitario) as $key) {
            if (!in_array((string) $key, $idsCarrinho, true)) {
                unset($this->preco_unitario[$key], $this->atualizar_preco_base[$key]);
            }
        }
    }

    private function aplicarPrecosEditadosNoCarrinho(): array
    {
        $carrinho = session('carrinho', []);

        foreach ($carrinho as $produtoId => $item) {
            $key = (string) $produtoId;
            $novoPreco = max(0, (float) ($this->preco_unitario[$key] ?? $item['preco_unitario'] ?? 0));
            $this->preco_unitario[$key] = $novoPreco;
            $carrinho[$produtoId]['preco_unitario'] = $novoPreco;
        }

        session(['carrinho' => $carrinho]);
        return $carrinho;
    }

    private function sincronizarValorVendaComCarrinho(): void
    {
        if ($this->valor_venda_editado) {
            return;
        }

        $carrinho = session('carrinho', []);
        $this->valor_venda = $this->totalItensCarrinho($carrinho);
    }

    private function normalizarFinanceiro(): array
    {
        $carrinho = session('carrinho', []);
        $subtotalItens = $this->totalItensCarrinho($carrinho);

        $frete = max(0, (float) ($this->frete ?? 0));
        $descontoPercentual = min(100, max(0, (float) ($this->desconto_percentual ?? 0)));

        $valorVendaBase = $this->valor_venda_editado
            ? max(0, (float) ($this->valor_venda ?? 0))
            : $subtotalItens;

        $descontoManual = round(($valorVendaBase * $descontoPercentual) / 100, 2);
        $descontoCombo = round($this->calcularDescontoCombo($carrinho), 2);
        $descontoTotal = round($descontoManual + $descontoCombo, 2);

        $totalOriginal = round($valorVendaBase, 2);
        $totalFinal = max(0, round($totalOriginal - $descontoTotal, 2));

        return [
            'subtotal_itens' => round($subtotalItens, 2),
            'valor_venda_base' => round($valorVendaBase, 2),
            'frete' => round($frete, 2),
            'desconto_percentual' => round($descontoPercentual, 2),
            'desconto_manual' => $descontoManual,
            'desconto_combo' => $descontoCombo,
            'desconto_total' => $descontoTotal,
            'total_original' => $totalOriginal,
            'total_final' => $totalFinal,
        ];
    }

    public function updatedValorVenda($value): void
    {
        $this->valor_venda_editado = true;
        $this->valor_venda = max(0, (float) ($value ?? 0));
    }

    public function updatedFrete($value): void
    {
        $this->frete = max(0, (float) ($value ?? 0));
    }

    public function updatedDescontoPercentual($value): void
    {
        $this->desconto_percentual = min(100, max(0, (float) ($value ?? 0)));
    }

    public function updatedFormaPagamento($value): void
    {
        if ($value !== 'cartao_credito') {
            $this->parcelas_cartao = '';
        }
    }

    public function updatedPrecoUnitario($value, $key): void
    {
        $preco = max(0, (float) ($value ?? 0));
        $this->preco_unitario[(string) $key] = $preco;

        $carrinho = session('carrinho', []);
        if (isset($carrinho[$key])) {
            $carrinho[$key]['preco_unitario'] = $preco;
            session(['carrinho' => $carrinho]);
            $this->sincronizarValorVendaComCarrinho();
        }
    }

    public function confirmar()
    {
        // $this->validate();
        $carrinho = $this->aplicarPrecosEditadosNoCarrinho();

        if (empty($carrinho)) {
            return $this->dispatch('toast', ['type' => 'error', 'message' => 'Carrinho está vazio.']);
        }

        $usuario = auth()->user();
        $empresaId = $usuario->empresa_id ?? 1;
        $lojaIdVenda = null;
        $lojaVinculadaValida = !empty($usuario->loja_id)
            && Loja::where('empresa_id', $empresaId)->where('id', $usuario->loja_id)->exists();

        if ($lojaVinculadaValida) {
            $lojaIdVenda = $usuario->loja_id;
        } else {
            $lojaIdVenda = $this->loja_id;
        }

        $lojaValida = !empty($lojaIdVenda) && Loja::where('empresa_id', $empresaId)->where('id', $lojaIdVenda)->exists();
        if (!$lojaValida) {
            return $this->dispatch('toast', ['type' => 'error', 'message' => 'Selecione a loja para emitir a nota corretamente.']);
        }

        if ($this->forma_pagamento === 'cartao_credito') {
            $parcelas = (int) ($this->parcelas_cartao ?? 0);
            if ($parcelas < 1 || $parcelas > 24) {
                return $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Informe a quantidade de parcelas do cartão de crédito (1 a 24).'
                ]);
            }
        }

        DB::beginTransaction();

        try {
            $financeiro = $this->normalizarFinanceiro();

            $produtos = Produto::with('categoria')
                ->whereIn('id', collect($carrinho)->pluck('produto_id')->unique())
                ->get()
                ->keyBy('id');

            $totaisPorCategoria = [];
            foreach ($carrinho as $item) {
                $produto = $produtos->get($item['produto_id']);
                if (!$produto || !$produto->categoria_id) {
                    continue;
                }
                $totaisPorCategoria[$produto->categoria_id] = ($totaisPorCategoria[$produto->categoria_id] ?? 0)
                    + (int) $item['quantidade'];
            }

            $categorias = Categoria::whereIn('id', array_keys($totaisPorCategoria))->get()->keyBy('id');
            $violacoes = [];
            foreach ($totaisPorCategoria as $categoriaId => $quantidade) {
                $categoria = $categorias->get($categoriaId);
                $limite = (int) ($categoria?->limite_venda_padrao ?? 0);
                if ($limite > 0 && $quantidade > $limite) {
                    $violacoes[] = [
                        'categoria_id' => $categoriaId,
                        'categoria' => $categoria?->nome,
                        'limite' => $limite,
                        'quantidade' => $quantidade,
                    ];
                }
            }

            $precisaAprovacao = !empty($violacoes);
            $venda = Venda::create([
                'empresa_id' => $empresaId,
                'loja_id' => $lojaIdVenda,
                'user_id' => auth()->id(),
                'cliente_id' => $this->cliente_id ?: null,
                'protocolo' => $this->protocolo,
                'valor_total' => $financeiro['total_original'],
                'frete' => $financeiro['frete'],
                'desconto' => $financeiro['desconto_total'],
                'valor_final' => $financeiro['total_final'],
                'forma_pagamento' => $this->forma_pagamento ?: null,
                'status_pagamento' => $this->status_pagamento ?: null,
                'parcelas_cartao' => $this->forma_pagamento === 'cartao_credito'
                    ? (int) $this->parcelas_cartao
                    : null,
                'status' => 'aberta',
                'aprovacao_status' => $precisaAprovacao ? 'pendente' : null,
                'aprovacao_detalhes' => $precisaAprovacao ? $violacoes : null,
            ]);

            foreach ($carrinho as $item) {
                $produto = Produto::findOrFail($item['produto_id']);

                $vendaItem = VendaItem::create([
                    'venda_id' => $venda->id,
                    'produto_id' => $produto->id,
                    'quantidade' => $item['quantidade'],
                    'valor_unitario' => $item['preco_unitario'],
                    'valor_total' => $item['preco_unitario'] * $item['quantidade'],
                ]);

                $key = (string) $produto->id;
                if (!empty($this->atualizar_preco_base[$key])) {
                    $produto->update([
                        'valor_venda' => $item['preco_unitario'],
                        'preco' => $item['preco_unitario'],
                    ]);
                }

                VendaEstoqueService::aplicarSaidaItem($vendaItem, (int) $item['quantidade']);
            }

            session()->forget('carrinho');
            $this->dispatch('atualizarCarrinho');

            if ($precisaAprovacao) {
                $admins = User::where('perfil', 'admin')->get();
                foreach ($admins as $admin) {
                    Notificacao::create([
                        'user_id' => $admin->id,
                        'titulo' => 'Venda pendente de aprovação',
                        'mensagem' => "Venda #{$venda->id} excedeu o limite da categoria e precisa de aprovação.",
                        'tipo' => 'venda.aprovacao',
                        'dados' => ['venda_id' => $venda->id, 'violacoes' => $violacoes],
                    ]);
                }

                DB::commit();

                return $this->dispatch('toast', [
                    'type' => 'warning',
                    'message' => 'Venda enviada para aprovação do administrador.',
                ]);
            }

            DB::commit();

            return $this->dispatch('toast', [
                'type' => 'success',
                'message' => $financeiro['desconto_total'] > 0
                    ? "Venda concluída com desconto de R$ " . number_format($financeiro['desconto_total'], 2, ',', '.')
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
        $this->sincronizarPrecosComCarrinho();
        $this->sincronizarValorVendaComCarrinho();

        $carrinho = session('carrinho', []);
        $clientes = Cliente::orderBy('nome')->get(); // 👈 lista de clientes
        $empresaId = auth()->user()->empresa_id ?? 1;
        $lojas = Loja::where('empresa_id', $empresaId)->orderBy('nome')->get();
        $disponiveis = [];

        foreach ($carrinho as $item) {

            $produto = Produto::findOrFail($item['produto_id']);

            $disponiveis[$item['produto_id']] = $produto ? $produto->disponivel_para_venda : 0;
        }

        $financeiro = $this->normalizarFinanceiro();

        return view('livewire.carrinho.confirmar-venda', [
            'itens' => $carrinho,
            'clientes' => $clientes,
            'lojas' => $lojas,
            'usuarioTemLojaVinculada' => $this->usuario_tem_loja_vinculada,
            'nomeLojaVinculada' => $this->nome_loja_vinculada,
            'total' => $financeiro['subtotal_itens'],
            'financeiro' => $financeiro,
            'disponiveis' => $disponiveis,
        ]);
    }
}
