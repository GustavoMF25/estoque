<?php

namespace App\Http\Controllers;

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
        return view('configurar.usuario.create');
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
        ]);
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'cpf' => $validated['cpf'] ?? null,
            'status' => $validated['status'],
            'perfil' => $validated['perfil'],
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
