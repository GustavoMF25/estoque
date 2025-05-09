<?php

namespace App\Http\Controllers;

use App\Models\Estoque;
use App\Models\Produto;
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
        $request->validate([
            'nome' => 'required|string|max:255',
            'codigo_barras' => 'nullable|string|max:50',
            'unidade' => 'required|string|max:10',
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
            Produto::create([
                'nome' => $request->nome,
                'codigo_barras' => $request->codigo_barras,
                'unidade' => $request->unidade,
                'preco' => $request->preco,
                'estoque_id' => $request->estoque_id,
                'ativo' => $request->ativo ?? true,
                'imagem' => $imagem,
            ]);
        }

        return redirect()->route('produtos.index')->with('success', 'Produtos cadastrados com sucesso!');
    }

    public function destroy(Produto $produto)
    {
        // Se quiser excluir a imagem do storage
        if ($produto->imagem && \Storage::disk('public')->exists($produto->imagem)) {
            \Storage::disk('public')->delete($produto->imagem);
        }

        $produto->delete();

        return redirect()->route('produtos.index')->with('success', 'Produto excluído com sucesso!');
    }
}
