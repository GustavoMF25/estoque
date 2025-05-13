<?php

namespace App\Http\Controllers;

use App\Models\Estoque;
use App\Models\Loja;
use Exception;
use Illuminate\Http\Request;

class EstoqueController extends Controller
{
    public function index()
    {
        return view('estoque.index');
    }

    public function create()
    {
        return view('estoque.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'localizacao' => 'required|string|max:150',
            'quantidade_maxima' => 'nullable|integer|min:0',
            'loja_id' => 'nullable|exists:lojas,id',
        ]);

        Estoque::create($request->only('nome', 'descricao', 'quantidade_maxima', 'loja_id', 'localizacao'));

        return redirect()->route('estoques.index')->with('success', 'Estoque criado com sucesso!');
    }

    public function edit(Estoque $estoque)
    {
        $lojas = Loja::all();
        return view('estoques.edit', compact('estoque', 'lojas'));
    }

    public function update(Request $request, Estoque $estoque)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'quantidade_maxima' => 'nullable|integer',
            'loja_id' => 'nullable|exists:lojas,id',
        ]);

        $estoque->update($request->only('nome', 'descricao', 'quantidade_maxima', 'loja_id'));

        return redirect()->route('estoques.index')->with('success', 'Estoque atualizado com sucesso!');
    }

    public function destroy(Estoque $estoque)
    {
        try {
            if (optional(auth()->user())->isAdmin()) {
                $estoque->delete();
                return redirect()->route('estoques.index')->with('success', 'Estoque removido com sucesso!');
            }else{
            return redirect()->route('estoques.index')->with('error', 'Estoque não removido, sem permissão.');    
            }
        } catch (Exception $err) {
            return redirect()->route('estoques.index')->with('error', 'Estoque não removido ' . $err->getMessage());
        }
    }

    public function restore($id)
    {
        $estoque = Estoque::withTrashed()->findOrFail($id);
        $estoque->restore();

        return redirect()->route('estoques.index')->with('success', 'Estoque restaurado com sucesso!');
    }
}
