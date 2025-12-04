<?php

namespace App\Livewire\Produto;

use App\Models\Produto;
use App\Services\ProdutoUnidadeService;
use Livewire\Component;

class ModalAdicionarProduto extends Component
{

    public $nome;
    public $id;
    public $quantidade = 1;
    public $produto;
    public $mensagem;

    protected $rules = [
        'quantidade' => 'required|integer|min:1',
    ];

    public function mount($nome, $id)
    {
        $this->nome = $nome;
        $this->id = $id;
    }

    public function adicionar()
    {
        try {
            $this->produto = Produto::where('nome', $this->nome)->firstOrFail();

            $quantidade = (int) $this->quantidade;

            if ($quantidade <= 0) {
                return $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Informe uma quantidade válida.'
                ]);
            }

            ProdutoUnidadeService::adicionarUnidades(
                $this->produto,
                $quantidade,
                "Adição manual de {$quantidade} unidade(s) ao produto {$this->produto->nome}"
            );

            $this->mensagem = "{$quantidade} novas unidades adicionadas ao produto '{$this->produto->nome}'.";
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
        return view('livewire.produto.modal-adicionar-produto');
    }
}
