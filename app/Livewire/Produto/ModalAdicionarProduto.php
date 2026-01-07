<?php

namespace App\Livewire\Produto;

use App\Models\Produto;
use App\Services\AuditLogger;
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

            AuditLogger::info('produto.unidades.adicao.iniciada', [
                'produto_id' => $this->produto->id,
                'quantidade' => $quantidade,
            ]);

            ProdutoUnidadeService::adicionarUnidades(
                $this->produto,
                $quantidade,
                "Adição manual de {$quantidade} unidade(s) ao produto {$this->produto->nome}"
            );

            AuditLogger::info('produto.unidades.adicao.concluida', [
                'produto_id' => $this->produto->id,
                'quantidade' => $quantidade,
            ]);

            $this->mensagem = "{$quantidade} novas unidades adicionadas ao produto '{$this->produto->nome}'.";
            $this->dispatch('fecharModal');

            return $this->dispatch('toast', [
                'type' => 'success',
                'message' => $this->mensagem,
            ]);
        } catch (\Exception $e) {
            AuditLogger::info('produto.unidades.adicao.falhou', [
                'nome' => $this->nome,
                'mensagem' => $e->getMessage(),
            ]);

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
