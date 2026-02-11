<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Estoque;
use App\Models\Fabricante;
use App\Models\Produto;
use App\Services\AuditLogger;
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

    public function catalogo()
    {
        return view('produto.catalogo');
    }

    public function create()
    {
        $estoques = Estoque::all();
        $categorias = Categoria::where('ativo', true)->orderBy('nome')->get();
        $fabricantes = Fabricante::orderBy('nome')->get();
        return view('produto.create', compact(['estoques', 'categorias', 'fabricantes']));
    }

    public function show(Request $request)
    {
        return view('produto.show', $request->all());
    }

    public function store(Request $request)
    {
        try {
            ProdutosService::cadProdutoRequest($request);
            return redirect()->route('produtos.index')->with('success', 'Produtos cadastrados com sucesso!');    
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

                AuditLogger::info('produto.deleted', [
                    'produto_id' => $produto->id,
                ]);

                return redirect()->route('produtos.index')->with('success', 'Produto excluído com sucesso!');
            }
            return redirect()->route('produtos.index')->with('error', 'Sem permissão para remover.');
        } catch (Exception $err) {
            return redirect()->route('produtos.index')->with('error', 'Produtos não removido ' . $err->getMessage());
        }
    }

    public function desativar(Produto $produto)
    {
        try {
            if (!optional(auth()->user())->isAdmin()) {
                return redirect()->route('produtos.index')->with('error', 'Sem permissão para desativar.');
            }

            if (!$produto->ativo) {
                return redirect()->route('produtos.index')->with('success', 'Produto já está desativado.');
            }

            $produto->update(['ativo' => false]);

            MovimentacaoService::registrar([
                'produto_id' => $produto->id,
                'tipo' => 'cancelamento',
                'quantidade' => 1,
                'observacao' => 'Produto desativado pelo administrador',
            ]);

            AuditLogger::info('produto.deactivated', [
                'produto_id' => $produto->id,
            ]);

            return redirect()->route('produtos.index')->with('success', 'Produto desativado com sucesso!');
        } catch (Exception $err) {
            return redirect()->route('produtos.index')->with('error', 'Produto não desativado ' . $err->getMessage());
        }
    }

    public function vender(Request $request)
    {
        try {
            $request->validate([
                'nome' => 'required|string',
                'quantidade' => 'required|integer|min:1',
            ]);

            $quantidadeRestante = $request->quantidade;

            // Busca os produtos com o nome fornecido
            $produtos = Produto::Ativo()
                ->where('nome', $request->nome)
                ->whereHas('ultimaMovimentacao', function ($query) {
                    $query->where('tipo', 'disponivel');
                })->get();

            foreach ($produtos as $produto) {
                if ($quantidadeRestante <= 0) {
                    break;
                }
                MovimentacaoService::registrar([
                    'produto_id' => $produto->id,
                    'tipo' => 'saida',
                    'quantidade' => 1,
                    'observacao' => 'Vendido',
                ]);
                $quantidadeRestante--;
            }

            $vendido = $request->quantidade - $quantidadeRestante;
            AuditLogger::info('produto.venda.realizada', [
                'nome' => $request->nome,
                'quantidade_solicitada' => $request->quantidade,
                'quantidade_vendida' => $vendido,
            ]);
            return redirect()->back()->with('success', 'Venda registrada com sucesso!');
        } catch (Exception $err) {

            return redirect()->back()->with('error', 'Produtos não vendido ' . $err->getMessage());
        }
    }
}
