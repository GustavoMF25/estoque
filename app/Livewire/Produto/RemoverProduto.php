<?php

namespace App\Livewire\Produto;

use App\Models\Produto;
use App\Models\ProdutosUnidades;
use App\Services\ProdutoUnidadeService;
use Livewire\Component;

class RemoverProduto extends Component
{
    public $nome;
    public $id;
    public $quantidade = 1;
    public $qtdMax;
    public $observacao = '';
    public $novo_status = 'disponivel';
    public $produto;
    public $mensagem;

    protected $rules = [
        'quantidade' => 'required|integer|min:1',
    ];

    public function mount($nome, $id)
    {
        $this->nome = $nome;
        $this->id = $id;
        $this->produto = Produto::find($id);
        $this->qtdMax = $this->produto->unidades()
            ->where('status', 'disponivel')
            ->count();
    }

    public function remover()
    {
        try {
            $this->produto = Produto::where('nome', $this->nome)->firstOrFail();
            $quantidade = (int) $this->quantidade;

            if ($quantidade > $this->qtdMax) {
                return $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Quantidade informada maior que a permitida.'
                ]);
            }

            $unidadesDisponiveis = ProdutosUnidades::where('produto_id', $this->produto->id)
                ->where('status', 'disponivel')
                ->limit($quantidade)
                ->get();

            $novoStatus = $this->novo_status ?: 'reservado';

            ProdutoUnidadeService::alterarStatus(
                $unidadesDisponiveis,
                $novoStatus,
                ProdutoUnidadeService::tipoMovimentacaoPorStatus($novoStatus),
                $this->observacao ?: "Removidas {$quantidade} unidade(s) de '{$this->produto->nome}'"
            );

            // ✅ Mensagem de sucesso
            $this->mensagem = "{$quantidade} unidade(s) do produto '{$this->produto->nome}' movidas para '{$novoStatus}'.";
            $this->dispatch('fecharModal');

            return $this->dispatch('toast', [
                'type' => 'success',
                'message' => $this->mensagem,
            ]);
        } catch (\Exception $e) {
            return $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao adicionar unidades: ' . $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.produto.remover-produto');
    }
}
