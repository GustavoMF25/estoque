<?php

namespace App\Http\Controllers;

use App\Models\Perfil;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PerfilController extends Controller
{
    public function index()
    {
        $perfis = Perfil::query()->orderBy('nome')->get();

        return view('perfis.index', compact('perfis'));
    }

    public function create()
    {
        return view('perfis.create');
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:80', 'alpha_dash', Rule::unique('perfis', 'slug')],
            'descricao' => ['nullable', 'string', 'max:500'],
            'ativo' => ['nullable', 'boolean'],
        ]);

        $dados['ativo'] = (bool) ($dados['ativo'] ?? false);

        Perfil::create($dados);

        return redirect()->route('perfis.index')->with('success', 'Perfil criado com sucesso.');
    }

    public function edit(Perfil $perfil)
    {
        return view('perfis.edit', compact('perfil'));
    }

    public function update(Request $request, Perfil $perfil)
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:80', 'alpha_dash', Rule::unique('perfis', 'slug')->ignore($perfil->id)],
            'descricao' => ['nullable', 'string', 'max:500'],
            'ativo' => ['nullable', 'boolean'],
        ]);

        if ($perfil->slug === 'admin' && ($dados['slug'] ?? 'admin') !== 'admin') {
            return back()->withErrors(['slug' => 'O perfil admin não pode ter o slug alterado.'])->withInput();
        }

        $dados['ativo'] = (bool) ($dados['ativo'] ?? false);
        $perfil->update($dados);

        return redirect()->route('perfis.index')->with('success', 'Perfil atualizado com sucesso.');
    }

    public function destroy(Perfil $perfil)
    {
        if ($perfil->slug === 'admin') {
            return back()->with('error', 'O perfil admin não pode ser removido.');
        }

        if (\App\Models\User::query()->where('perfil', $perfil->slug)->exists()) {
            return back()->with('error', 'Não é possível remover este perfil pois existem usuários vinculados.');
        }

        $perfil->delete();

        return redirect()->route('perfis.index')->with('success', 'Perfil removido com sucesso.');
    }
}
