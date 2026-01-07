<?php

namespace App\Livewire\Produto;

use App\Models\Produto;
use App\Services\AuditLogger;
use App\Services\ProdutoUnidadeService;
use Livewire\Component;

class ModalApagarProduto extends Component
{

    public $nome;
    public $id;


    public function mount($nome, $id)
    {
        $this->nome = $nome;
        $this->id = $id;
    }

    public function apagar()
    {
        try {
            $produto = Produto::find($this->id);
            if (optional(auth()->user())->isAdmin()) {
                $unidades = $produto->unidades()->get();

                ProdutoUnidadeService::alterarStatus(
                    $unidades,
                    'indisponivel',
                    ProdutoUnidadeService::tipoMovimentacaoPorStatus('indisponivel'),
                    'Produto removido e unidades marcadas como indisponíveis'
                );

                $produto->ativo = false;
                $produto->save();

                $produto->delete();

                AuditLogger::info('produto.apagado.modal', [
                    'produto_id' => $produto->id,
                ]);

                $this->dispatch('fecharModal');

                return $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Produto excluído com sucesso!',
                ]);
            }
            AuditLogger::info('produto.apagar.nao_autorizado', [
                'produto_id' => $produto->id ?? null,
            ]);
            return $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Você não tem permissão para remover o produto.',
            ]);
        } catch (\Exception $e) {
            AuditLogger::info('produto.apagar.falhou', [
                'produto_id' => $this->id,
                'mensagem' => $e->getMessage(),
            ]);

            return $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao remover produto: ' . $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.produto.modal-apagar-produto');
    }
}
