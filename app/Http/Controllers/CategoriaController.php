<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Produto;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = Categoria::orderBy('nome')->paginate(10);
        return view('categorias.index', compact('categorias'));
    }

    public function create()
    {
        return view('categorias.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'ativo' => 'required|boolean',
        ]);

        Categoria::create($request->only('nome', 'descricao', 'ativo'));

        return redirect()->route('categorias.index')->with('success', 'Categoria criada com sucesso.');
    }

    public function edit(Categoria $categoria)
    {
        return view('categorias.edit', compact('categoria'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'ativo' => 'required|boolean',
        ]);

        $categoria->update($request->only('nome', 'descricao', 'ativo'));

        return redirect()->route('categorias.index')->with('success', 'Categoria atualizada com sucesso.');
    }
    
    public function destroy(Categoria $categoria)
    {
        // Verifica se há produtos associados à categoria
        $temProdutos = Produto::where('categoria_id', $categoria->id)->exists();

        if ($temProdutos) {
            return redirect()->route('categorias.index')->with('error', 'Não é possível excluir esta categoria, pois existem produtos vinculados a ela.');
        }

        $categoria->delete();

        return redirect()->route('categorias.index')->with('success', 'Categoria excluída com sucesso.');
    }
}
