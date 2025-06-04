<?php

namespace App\Livewire\Produto;

use App\Models\Produto;
use Livewire\Component;

class ProdutoVisualizar extends Component
{
    public $produtos;
    public $nome;

    public function mount($nome = null, $estoque_id = null, $ultima_movimentacao = null)
    {
        $query = Produto::with(['estoque', 'ultimaMovimentacao'])->withTrashed();

        if (!empty($nome)) {
            $query->where('produtos.nome', 'LIKE', "%{$nome}%");
        }

        if (!empty($estoque_id)) {
            $query->where('produtos.estoque_id', $estoque_id);
        }
        if (!empty($ultima_movimentacao)) {
            $query->whereHas('ultimaMovimentacao', function ($q) use($ultima_movimentacao) {
                $q->where('tipo', $ultima_movimentacao);
            });
        }
        $query->select([
            'produtos.id',
            'produtos.nome',
            'produtos.imagem',
            'produtos.preco',
            'produtos.estoque_id',
            'produtos.created_at',
        ]);

        $this->produtos = $query->get();
        $this->nome = $nome;
    }

    public function render()
    {
        return view('livewire.produto.produto-visualizar');
    }
}
