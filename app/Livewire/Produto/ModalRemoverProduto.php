<?php

namespace App\Livewire\Produto;

use App\Models\Produto;
use App\Models\ProdutosUnidades;
use App\Services\MovimentacaoService;
use App\Services\ProdutosService;
use App\Services\ProdutoUnidadeService;
use Livewire\Component;

class ModalRemoverProduto extends Component
{
    public $nome;
    public $id;
    public $quantidade = 1;
    public $observacao = 1;
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

    public function remover()
    {
        try {
            // 🔍 Busca o produto base pelo nome
            $this->produto = Produto::where('nome', $this->nome)->firstOrFail();
            // 🔢 Quantidade de unidades que serão adicionadas
            $quantidade = (int) $this->quantidade;
            if ($quantidade <= 0) {
                return $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Informe uma quantidade válida.'
                ]);
            }

            // // ⚙️ Cria novas unidades físicas (produtos_unidades)
            // $ultimaUnidade = $this->produto->unidades()->orderByDesc('id')->first();
            // $indiceBase = $ultimaUnidade ? $ultimaUnidade->id - 1 : 1;

            // for ($i = 0; $i < $quantidade; $i++) {
            //     $codigo = ProdutoUnidadeService::gerarCodigo(
            //         $this->produto,
            //         $indiceBase + $i
            //     );
            //     ProdutosUnidades::create([
            //         'produto_id' => $this->produto->id,
            //         'codigo_unico' => $codigo,
            //         'status' => 'disponivel',
            //     ]);
            // }
            MovimentacaoService::registrar([
                'produto_id' => $this->produto->id,
                'tipo' => 'entrada',
                'quantidade' => $quantidade,
                'observacao' => "Adição de {$quantidade} unidades ao produto {$this->produto->nome}",
            ]);

            MovimentacaoService::registrar([
                'produto_id' => $this->produto->id,
                'tipo' => 'disponivel',
                'quantidade' => $quantidade,
                'observacao' => "Novas unidades disponíveis ({$quantidade}) adicionadas manualmente",
            ]);
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
