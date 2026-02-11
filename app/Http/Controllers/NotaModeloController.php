<?php

namespace App\Http\Controllers;

use App\Models\NotaModelo;
use Illuminate\Http\Request;

class NotaModeloController extends Controller
{
    public function index()
    {
        $modelos = NotaModelo::orderBy('nome')->paginate(15);
        return view('nota-modelos.index', compact('modelos'));
    }

    public function create()
    {
        return view('nota-modelos.create');
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'icone' => 'nullable|string|max:100',
            'conteudo_frente' => 'required|string',
            'conteudo_verso' => 'nullable|string',
            'ativo' => 'required|boolean',
        ]);

        $dados['conteudo_verso'] = $dados['conteudo_verso'] ?? '';
        NotaModelo::create($dados);

        return redirect()->route('nota-modelos.index')->with('success', 'Modelo criado com sucesso!');
    }

    public function edit(NotaModelo $nota_modelo)
    {
        return view('nota-modelos.edit', ['modelo' => $nota_modelo]);
    }

    public function update(Request $request, NotaModelo $nota_modelo)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'icone' => 'nullable|string|max:100',
            'conteudo_frente' => 'required|string',
            'conteudo_verso' => 'nullable|string',
            'ativo' => 'required|boolean',
        ]);

        $dados['conteudo_verso'] = $dados['conteudo_verso'] ?? $nota_modelo->conteudo_verso;
        $nota_modelo->update($dados);

        return redirect()->route('nota-modelos.index')->with('success', 'Modelo atualizado com sucesso!');
    }

    public function destroy(NotaModelo $nota_modelo)
    {
        $nota_modelo->delete();
        return redirect()->route('nota-modelos.index')->with('success', 'Modelo removido!');
    }
}
