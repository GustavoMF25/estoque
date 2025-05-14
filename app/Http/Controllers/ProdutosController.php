<?php

namespace App\Http\Controllers;

use App\Models\Estoque;
use App\Models\Produto;
use App\Services\MovimentacaoService;
use App\Services\ProdutosService;
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
            return ProdutosService::cadastraProduto($request);
        } catch (Exception $err) {
            return redirect()->route('produtos.index')->with('error', 'Produtos não cadastrados: ' . $err->getMessage());
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
