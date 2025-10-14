<?php

namespace App\Livewire\Produto;

use App\Models\Produto;
use App\Services\ProdutosService;
use Livewire\Component;

class ModalAdicionarProduto extends Component
{

    public $nome;
    public $quantidade = 1;
    public $produto;
    public $mensagem;

    protected $rules = [
        'quantidade' => 'required|integer|min:1',
    ];
    public function mount($nome)
    {
        $this->nome = $nome;
        // $this->quantidade = Produto::where('nome', $nome)
        //     ->whereHas('ultimaMovimentacao', function ($q) {
        //         $q->where('tipo', 'disponivel');
        //     })->count();
    }

    public function adicionar()
    {
        $this->produto = Produto::where('nome', $this->nome)->first();

        $dados = [
            'nome' => $this->produto->nome,
            'preco' => $this->produto->preco ?? 0,
            'estoque_id' => $this->produto->estoque_id,
            'quantidade' => $this->quantidade,
            'categoria_id' => $this->produto->categoria_id,
            'fabricante_id' => $this->produto->fabricante_id,
        ];

        ProdutosService::handleCadastroProduto($dados);

        $this->mensagem = "{$this->quantidade} produtos foram criados.";
        $this->dispatch('fecharModal');
        
        return $this->dispatch('toast', [
            'type' => 'success',
            'message' => $this->mensagem
        ]);
    }

    public function render()
    {
        return view('livewire.produto.modal-adicionar-produto');
    }
}
