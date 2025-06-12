<?php

namespace App\Livewire;

use App\Models\Estoque;
use App\Models\Produto;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProdutoEditar extends Component
{
    use WithFileUploads;

    public $produtoId;
    public $nome, $unidade, $preco, $estoque_id, $ativo;
    public $imagem, $imagemPreview;
    public $estoques;

    protected $rules = [
        'nome' => 'required|string|max:255',
        'unidade' => 'nullable|string|max:10',
        'preco' => 'required|numeric|min:0',
        'estoque_id' => 'required|exists:estoques,id',
        'ativo' => 'boolean',
        'imagem' => 'nullable|image|max:2048',
    ];

    public function mount($produtoId)
    {
        $produto = Produto::withTrashed()->findOrFail($produtoId);

        $this->produtoId = $produto->id;
        $this->nome = $produto->nome;
        $this->unidade = $produto->unidade;
        $this->preco = $produto->preco;
        $this->estoque_id = $produto->estoque_id;
        $this->ativo = $produto->ativo;
        $this->imagemPreview = $produto->imagem;
        $this->estoques = Estoque::pluck('nome', 'id');
    }

    public function salvar()
    {
        $this->validate();

        $produto = Produto::withTrashed()->findOrFail($this->produtoId);

        if ($this->imagem) {
            $this->imagemPreview = $this->imagem->store('produtos', 'public');
            $produto->imagem = $this->imagemPreview;
        }

        $produto->update([
            'nome' => $this->nome,
            'unidade' => $this->unidade ?? 'un',
            'preco' => $this->preco,
            'estoque_id' => $this->estoque_id,
            'ativo' => $this->ativo,
        ]);

        // dd($produto);

        $this->dispatch('refreshTabelaVisualizarProduto');
        $this->dispatch('$refresh');
        $this->dispatch('msgtSuccess', 'Produto atualizado com sucesso!');


        session()->flash('success', 'Produto atualizado com sucesso!');
    }

    public function render()
    {
        return view('livewire.produto-editar');
    }
}
