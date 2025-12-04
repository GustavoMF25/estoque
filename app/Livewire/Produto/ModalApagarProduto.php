<?php

namespace App\Livewire\Produto;

use App\Models\Produto;
use App\Models\ProdutosUnidades;
use App\Services\MovimentacaoService;
use App\Services\ProdutosService;
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
                $produto->unidades()->update([
                    'status' => 'indisponivel'
                ]);

                $produto->ativo = false;
                $produto->save();

                $produto->delete();

                MovimentacaoService::registrar([
                    'produto_id' => $produto->id,
                    'tipo' => 'cancelamento',
                    'quantidade' => $produto->unidades()->count(),
                    'observacao' => 'Produto removido e unidades marcadas como indisponíveis',
                ]);

                $this->dispatch('fecharModal');

                return $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Produto excluído com sucesso!',
                ]);
            }
            return $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Você não tem permissão para remover o produto.',
            ]);
        } catch (\Exception $e) {
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
