<?php

namespace App\Http\Controllers;

use App\Models\Estoque;
use App\Models\Produto;
use App\Services\MovimentacaoService;
use Exception;
use Illuminate\Http\Request;

class ProdutosController extends Controller
{

    public function index()
    {
        $produtos = Produto::with('estoque')->latest()->paginate(10);
        return view('produto.index', compact('produtos'));
    }

    public function create()
    {
        $estoques = Estoque::all();
        return view('produto.create', compact('estoques'));
    }


    public function store(Request $request)
    {
        try {
            $request->validate([
                'nome' => 'required|string|max:255',
                'codigo_barras' => 'nullable|string|max:50',
                'unidade' => 'nullable|string|max:10',
                'preco' => 'required|numeric|min:0',
                'estoque_id' => 'required|exists:estoques,id',
                'quantidade' => 'required|integer|min:1',
                'ativo' => 'boolean',
            ]);

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
            }

            return redirect()->route('produtos.index')->with('success', 'Produtos cadastrados com sucesso!');
        } catch (Exception $err) {
            return redirect()->route('produtos.index')->with('error', 'Produtos não cadastrados ' . $err->getMessage());
        }
    }

    public function destroy(Produto $produto)
    {
        try {
            if (optional(auth()->user())->isAdmin()) {
                $produto->ativo = false;
                $produto->delete();

                MovimentacaoService::registrar([
                    'produto_id' => $produto->id,
                    'tipo' => 'cancelamento',
                    'quantidade' => 1,
                    'observacao' => 'Remoção lógica via exclusão de produto',
                ]);

                return redirect()->route('produtos.index')->with('success', 'Produto excluído com sucesso!');
            }
            return redirect()->route('produtos.index')->with('error', 'Sem permissão para remover.');
        } catch (Exception $err) {
            return redirect()->route('produtos.index')->with('error', 'Produtos não removido ' . $err->getMessage());
        }
    }
}
