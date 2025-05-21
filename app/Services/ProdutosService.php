<?php

namespace App\Services;

use App\Models\Estoque;
use App\Models\Movimentacao;
use App\Models\Produto;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Auth;

class ProdutosService
{
    public static function cadastraProduto($request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'codigo_barras' => 'nullable|string|max:50',
            'unidade' => 'nullable|string|max:10',
            'preco' => 'required|numeric|min:0',
            'estoque_id' => 'required|exists:estoques,id',
            'quantidade' => 'required|integer|min:1',
            'ativo' => 'boolean',
        ]);

        $estoque = Estoque::withCount('produtos')->findOrFail($request->estoque_id);
        $totalAtual = $estoque->produtos_count;
        $quantidadeMaxima = $estoque->quantidade_maxima;

        if ($quantidadeMaxima > 0 && ($totalAtual + $request->quantidade) > $quantidadeMaxima) {
            return redirect()->route('produtos.index')->with('error', 'Não é possível cadastrar: o estoque excederia o limite máximo de ' . $quantidadeMaxima . ' itens.');
        }

        $imagem = null;

        if ($request->hasFile('imagem')) {
            $imagem = $request->file('imagem')->store('produtos', 'public');
        }

        for ($i = 0; $i < $request->quantidade; $i++) {
            $produto = Produto::create([
                'nome' => $request->nome,
                'codigo_barras' => Produto::gerarCodigoBarrasUnico(),
                'unidade' => $request->unidade ?? 'un',
                'preco' => $request->preco,
                'estoque_id' => $request->estoque_id,
                'ativo' => $request->ativo ?? true,
                'imagem' => $imagem,
            ]);

            MovimentacaoService::registrar([
                'produto_id' => $produto->id,
                'tipo' => 'entrada',
                'quantidade' => 1,
                'observacao' => 'Cadastro inicial do produto'
            ]);
            MovimentacaoService::registrar([
                'produto_id' => $produto->id,
                'tipo' => 'disponivel',
                'quantidade' => 1,
                'observacao' => 'Disponível para venda'
            ]);
        }
        
        return redirect()->route('produtos.index')->with('success', 'Produtos cadastrados com sucesso!');
    }
}
