<?php

namespace App\Livewire\Produto;

use App\Models\Categoria;
use App\Models\Estoque;
use App\Models\Fabricante;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Produto;
use App\Services\MovimentacaoService;
use App\Services\ProdutosService;
use Illuminate\Support\Facades\Storage;

class ModalAtualizarProduto extends Component
{
    use WithFileUploads;

    public $id;
    public $produto;
    public $nome;
    public $nome_atual;
    public $valor_entrada;
    public $valor_venda;
    public $quantidade = 1;
    public $imagem;
    public $estoque_id;
    public $fabricante;
    public $categoria;
    public $estoques;
    public $fabricantes;
    public $mensagem = 'Atualizado com sucesso';
    public $categorias;

    protected $rules = [
        'nome' => 'required|string|max:255',
        'valor_entrada' => 'nullable|numeric|min:0',
        'valor_venda' => 'nullable|numeric|min:0',
        'quantidade' => 'required|integer|min:1',
        'imagem' => 'nullable|image|max:2048',
        'estoque_id' => 'required|exists:estoques,id',
    ];

    public function mount($id)
    {
        $this->estoques = Estoque::all();
        $this->categorias = Categoria::all();
        $this->fabricantes = Fabricante::all();
        $this->id = $id;
        $this->produto = Produto::withTrashed()->findOrFail($this->id);

        $this->nome_atual = $this->produto->nome;
        $this->valor_entrada = $this->produto->valor_entrada ?? 0;
        $this->valor_venda = $this->produto->valor_venda ?? $this->produto->preco;
        $this->quantidade = $this->produto->disponiveis;
        $this->estoque_id = $this->produto->estoque_id;
        $this->fabricante = $this->produto->fabricante_id;
        $this->categoria = $this->produto->categoria_id;
    }

    public function atualizar()
    {
        $this->validate([
            'valor_entrada' => 'required|numeric|min:0',
            'valor_venda' => 'required|numeric|min:0',
            'nome' => 'required|string|max:255',
            'estoque_id' => 'required|exists:estoques,id',
            'categoria' => 'nullable|exists:categorias,id',
            'fabricante' => 'nullable|exists:fabricantes,id',
            'imagem' => 'nullable|image|max:2048', // até 2MB
        ]);
        try {
            $this->produto = Produto::withTrashed()->findOrFail($this->id);

            if ($this->imagem instanceof \Illuminate\Http\UploadedFile) {
                $path = $this->imagem->store('produtos', 'public');

                if ($this->produto->imagem && Storage::disk('public')->exists($this->produto->imagem)) {
                    Storage::disk('public')->delete($this->produto->imagem);
                }
                $this->produto->imagem = $path;
                $this->produto->save();
            }
            $this->produto->update([
                'preco' => $this->valor_venda ?? $this->produto->preco,
                'valor_entrada' => $this->valor_entrada ?? $this->produto->valor_entrada,
                'valor_venda' => $this->valor_venda ?? $this->produto->valor_venda,
                'nome' => $this->nome ?? $this->produto->nome,
                'estoque_id' => $this->estoque_id ?? $this->produto->estoque_id,
                'categoria_id' => $this->categoria ?? $this->produto->categoria_id,
                'fabricante_id' => $this->fabricante ?? $this->produto->fabricante_id,
                'imagem' => $this->produto->imagem,
            ]);

            return redirect()
                ->route('produtos.index')
                ->with('success', $this->mensagem ?? 'Produto atualizado com sucesso!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erro ao atualizar o produto: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.produto.modal-atualizar-produto');
    }
}
