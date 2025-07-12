<?php

namespace App\Livewire\Produto;

use App\Models\Estoque;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Produto;
use App\Services\MovimentacaoService;
use App\Services\ProdutosService;
use Illuminate\Support\Facades\Storage;

class ModalAtualizarProduto extends Component
{
    use WithFileUploads;

    public $nome;
    public $preco;
    public $quantidade = 1;
    public $imagem;
    public $estoque_id;
    public $estoques;
    public $ultimaMovimentacao;

    protected $rules = [
        'nome' => 'required|string|max:255',
        'preco' => 'nullable|numeric|min:0',
        'quantidade' => 'required|integer|min:1',
        'imagem' => 'nullable|image|max:2048',
        'estoque_id' => 'required|exists:estoques,id',
    ];

    public function mount($nome, $ultimaMovimentacao)
    {
        $this->estoques = Estoque::all();

        $this->nome = $nome;
        $this->ultimaMovimentacao = $ultimaMovimentacao;

        $produto = Produto::where('nome', $nome)->first();

        if ($produto) {
            $this->preco = $produto->preco;
            $this->quantidade = $produto->quantidade;
            $this->estoque_id = $produto->estoque_id;
        } else {
            session()->flash('error', 'Produto não encontrado.');
        }
    }

    public function atualizar()
    {
        $this->validate();

        $produtos = Produto::where('nome', $this->nome)
            ->whereHas('ultimaMovimentacao', function ($q) {
                $q->where('tipo', $this->ultimaMovimentacao);
            })
            ->get();

        $countAtual = $produtos->count();
        $quantidadeInformada = $this->quantidade;

        if ($quantidadeInformada > $countAtual) {
            $diferenca = $quantidadeInformada - $countAtual;

            $data = [
                'nome' => $this->nome,
                'preco' => $this->preco ?? 0,
                'estoque_id' => $this->estoque_id,
                'quantidade' => $diferenca,
            ];

            if ($this->imagem) {
                $path = $this->imagem->store('produtos', 'public');
                $data['imagem'] = $path;
            }

            ProdutosService::handleCadastroProduto($data);
            session()->flash('success', "{$diferenca} produtos foram criados para completar a quantidade informada.");
        }

        if ($quantidadeInformada < $countAtual) {
            // Calcula quantos precisam ser removidos
            $diferenca = $countAtual - $quantidadeInformada;

            $produtosParaRemover = Produto::where('nome', $this->nome)
                ->latest()
                ->take($diferenca)
                ->get();

            foreach ($produtosParaRemover as $produto) {

                MovimentacaoService::registrar([
                    'produto_id' => $produto->id,
                    'tipo' => 'cancelamento',
                    'quantidade' => 1,
                    'observacao' => 'Remoção lógica via exclusão de produto',
                ]);

                $produto->delete();
            }

            session()->flash('success', "{$diferenca} produtos foram removidos para ajustar à quantidade informada.");
        }

        Produto::where('nome', $this->nome)->update([
            'preco' => $this->preco ?? 0,
            'estoque_id' => $this->estoque_id,
        ]);

        if ($this->imagem) {
            $path = $this->imagem->store('produtos', 'public');

            Produto::where('nome', $this->nome)->chunkById(50, function ($produtos) use ($path) {
                foreach ($produtos as $produto) {
                    if ($produto->imagem && Storage::disk('public')->exists($produto->imagem)) {
                        Storage::disk('public')->delete($produto->imagem);
                    }
                    $produto->imagem = $path;
                    $produto->save();
                }
            });
        }

        // $this->dispatch('refreshProdutoVisualizar');
        // $this->dispatch('refreshProdutoVisualizar')->to('produto.produto-visualizar');

        $this->dispatch('refreshProdutoVisualizar')->to('produto.produto-visualizar', ['key' => 'produto-visualizar-' . $this->nome]);

        $this->reset(['nome', 'preco', 'quantidade', 'imagem', 'estoque_id']);
    }

    public function render()
    {
        return view('livewire.produto.modal-atualizar-produto');
    }
}
