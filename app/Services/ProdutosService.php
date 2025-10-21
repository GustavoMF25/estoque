<?php

namespace App\Services;

use App\Models\Estoque;
use App\Models\Movimentacao;
use App\Models\Produto;
use Exception;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Auth;

class ProdutosService
{
    public static function cadastraProduto($request)
    {
        try {
            $request->validate([
                'nome' => 'required|string|max:255',
                'codigo_barras' => 'nullable|string|max:50',
                'unidade' => 'nullable|string|max:10',
                'preco' => 'required|numeric|min:0',
                'estoque_id' => 'required|exists:estoques,id',
                'categoria_id' => 'required|exists:categoria,id',
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
                    'categoria_id' => $request->categoria_id,
                    'ativo' => $request->ativo ?? true,
                    'imagem' => $imagem,
                ]);

                $movimentacao = MovimentacaoService::registrar([
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
        } catch (Exception $err) {
            return $err->getMessage();
        }
    }

    public static function handleCadastroProduto(array $data)
    {
        $estoque = Estoque::withCount('produtos')->findOrFail($data['estoque_id']);
        $totalAtual = $estoque->produtos_count;
        $quantidadeMaxima = $estoque->quantidade_maxima;

        if ($quantidadeMaxima > 0 && ($totalAtual + 1) > $quantidadeMaxima) {
            return redirect()->route('produtos.index')
                ->with('error', 'Não é possível cadastrar: o estoque excederia o limite máximo de ' . $quantidadeMaxima . ' itens.');
        }

        // Trata imagem
        $imagem = $data['imagem'] ?? null;
        if ($imagem instanceof \Illuminate\Http\UploadedFile) {
            $imagem = $imagem->store('produtos', 'public');
        }

        // ✅ Cria apenas um produto
        $produto = Produto::create([
            'nome' => $data['nome'],
            'codigo_barras' => Produto::gerarCodigoBarrasUnico(),
            'unidade' => $data['unidade'] ?? 'un',
            'preco' => $data['preco'],
            'estoque_id' => $data['estoque_id'],
            'categoria_id' => $data['categoria_id'],
            'fabricante_id' => $data['fabricante_id'],
            'ativo' => $data['ativo'] ?? true,
            'imagem' => $imagem,
        ]);

        // ✅ Cria movimentações referentes à quantidade informada
        $quantidade = (int) $data['quantidade'];

        for ($i = 0; $i < $data['quantidade']; $i++) {
            MovimentacaoService::registrar([
                'produto_id' => $produto->id,
                'tipo' => 'entrada',
                'quantidade' => 1,
                'observacao' => 'Cadastro inicial do produto',
            ]);

            MovimentacaoService::registrar([
                'produto_id' => $produto->id,
                'tipo' => 'disponivel',
                'quantidade' => 1,
                'observacao' => 'Unidade disponível para venda',
            ]);
        }


        return true;
    }

    public static function cadProdutoRequest($request)
    {
        try {
            // dd($request->all());
            $validated = $request->validate([
                'nome' => 'required|string|max:255',
                'codigo_barras' => 'nullable|string|max:50',
                'unidade' => 'nullable|string|max:10',
                'preco' => 'required|numeric|min:0',
                'estoque_id' => 'required|exists:estoques,id',
                'quantidade' => 'required|integer|min:1',
                'categoria_id' => 'nullable|exists:categorias,id',
                'fabricante_id' => 'nullable|exists:fabricantes,id',
                'ativo' => 'boolean',
                'imagem' => 'nullable|image|max:2048',
            ]);
            // dd($validated);

            $validated['imagem'] = $request->file('imagem') ?? null;

            return self::handleCadastroProduto($validated);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
