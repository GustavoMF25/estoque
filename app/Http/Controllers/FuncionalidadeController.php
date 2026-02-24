<?php

namespace App\Http\Controllers;

use App\Models\Funcionalidade;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FuncionalidadeController extends Controller
{
    public function index()
    {
        $funcionalidades = Funcionalidade::query()
            ->orderBy('modulo')
            ->orderBy('ordem')
            ->orderBy('nome')
            ->get();

        return view('funcionalidades.index', compact('funcionalidades'));
    }

    public function create()
    {
        return view('funcionalidades.create');
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('funcionalidades', 'slug')],
            'descricao' => ['nullable', 'string', 'max:500'],
            'modulo' => ['nullable', 'string', 'max:100'],
            'rota' => ['nullable', 'string', 'max:255', Rule::unique('funcionalidades', 'rota')],
            'icone' => ['nullable', 'string', 'max:100'],
            'ordem' => ['nullable', 'integer', 'min:0'],
            'visivel_menu' => ['nullable', 'boolean'],
            'ativo' => ['nullable', 'boolean'],
        ]);

        $dados['ordem'] = $dados['ordem'] ?? 0;
        $dados['visivel_menu'] = (bool) ($dados['visivel_menu'] ?? false);
        $dados['ativo'] = (bool) ($dados['ativo'] ?? false);

        Funcionalidade::create($dados);

        return redirect()->route('funcionalidades.index')->with('success', 'Funcionalidade criada com sucesso.');
    }

    public function edit(Funcionalidade $funcionalidade)
    {
        return view('funcionalidades.edit', compact('funcionalidade'));
    }

    public function update(Request $request, Funcionalidade $funcionalidade)
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('funcionalidades', 'slug')->ignore($funcionalidade->id)],
            'descricao' => ['nullable', 'string', 'max:500'],
            'modulo' => ['nullable', 'string', 'max:100'],
            'rota' => ['nullable', 'string', 'max:255', Rule::unique('funcionalidades', 'rota')->ignore($funcionalidade->id)],
            'icone' => ['nullable', 'string', 'max:100'],
            'ordem' => ['nullable', 'integer', 'min:0'],
            'visivel_menu' => ['nullable', 'boolean'],
            'ativo' => ['nullable', 'boolean'],
        ]);

        $dados['ordem'] = $dados['ordem'] ?? 0;
        $dados['visivel_menu'] = (bool) ($dados['visivel_menu'] ?? false);
        $dados['ativo'] = (bool) ($dados['ativo'] ?? false);

        $funcionalidade->update($dados);

        return redirect()->route('funcionalidades.index')->with('success', 'Funcionalidade atualizada com sucesso.');
    }

    public function destroy(Funcionalidade $funcionalidade)
    {
        $funcionalidade->delete();

        return redirect()->route('funcionalidades.index')->with('success', 'Funcionalidade removida com sucesso.');
    }
}
