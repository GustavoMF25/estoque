<?php

namespace App\Http\Controllers;

use App\Models\Loja;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = User::all();
        return view('configurar.usuario.index', compact('usuarios'));
    }

    public function create()
    {
        $empresaId = auth()->user()->empresa_id ?? 1;
        $lojas = Loja::where('empresa_id', $empresaId)->orderBy('nome')->get();
        return view('configurar.usuario.create', compact('lojas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'cpf' => 'nullable|string|max:14',
            'status' => 'required|string|in:ativo,inativo',
            'perfil' => 'required|string|in:admin,gerente,operador,vendedor',
            'loja_id' => 'nullable|exists:lojas,id',
        ]);

        $empresaId = auth()->user()->empresa_id ?? 1;
        $lojaValida = null;
        if (!empty($validated['loja_id'])) {
            $lojaValida = Loja::where('empresa_id', $empresaId)->where('id', $validated['loja_id'])->exists();
            if (!$lojaValida) {
                return back()->withErrors(['loja_id' => 'A loja selecionada não pertence à sua empresa.'])->withInput();
            }
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'cpf' => $validated['cpf'] ?? null,
            'status' => $validated['status'],
            'perfil' => $validated['perfil'],
            'loja_id' => $validated['loja_id'] ?? null,
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuário cadastrado com sucesso.');
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return redirect()->route('usuarios.index')
                ->with('success', 'Usuário excluído com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('usuarios.index')
                ->with('error', 'Erro ao excluir o usuário: ' . $e->getMessage());
        }
    }
}
