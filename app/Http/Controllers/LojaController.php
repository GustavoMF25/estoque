<?php

namespace App\Http\Controllers;

use App\Models\Loja;
use App\Models\Empresa;
use Illuminate\Http\Request;

class LojaController extends Controller
{
    public function index()
    {
        $lojas = Loja::with('empresa')->get();
        return view('lojas.index', compact('lojas'));
    }

    public function create()
    {
        return view('lojas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'endereco' => 'nullable|string',
            'telefone' => 'nullable|string',
        ]);

        Loja::create([
            'empresa_id' => auth()->user()->empresa_id ?? 1,
            'nome' => $request->nome,
            'endereco' => $request->endereco,
            'telefone' => $request->telefone,
        ]);

        return redirect()->route('lojas.index')->with('success', 'Loja criada com sucesso!');
    }

    public function edit(Loja $loja)
    {
        return view('lojas.edit', compact('loja'));
    }

    public function update(Request $request, Loja $loja)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'endereco' => 'nullable|string',
            'telefone' => 'nullable|string',
        ]);

        $loja->update($request->only('nome', 'endereco', 'telefone'));

        return redirect()->route('lojas.index')->with('success', 'Loja atualizada com sucesso!');
    }

    public function destroy(Loja $loja)
    {
        $loja->delete();
        return redirect()->route('lojas.index')->with('success', 'Loja removida com sucesso!');
    }
}
