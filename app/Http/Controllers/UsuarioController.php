<?php

namespace App\Http\Controllers;

use App\Models\Loja;
use App\Models\Perfil;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

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
        $perfis = Perfil::where('ativo', true)->orderBy('nome')->get();
        return view('configurar.usuario.create', compact('lojas', 'perfis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'cpf' => 'nullable|string|max:14',
            'status' => 'required|string|in:ativo,inativo',
            'perfil' => 'required|string|exists:perfis,slug',
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

            if ($this->usuarioPossuiVinculos($user)) {
                return $this->desativarUsuarioComVinculos($user);
            }

            $user->delete();

            return redirect()->route('usuarios.index')
                ->with('success', 'Usuário excluído com sucesso!');
        } catch (QueryException $e) {
            if ($this->isViolacaoChaveEstrangeira($e) && isset($user)) {
                return $this->desativarUsuarioComVinculos($user);
            }

            return redirect()->route('usuarios.index')
                ->with('error', 'Erro ao excluir o usuário: ' . $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->route('usuarios.index')
                ->with('error', 'Erro ao excluir o usuário: ' . $e->getMessage());
        }
    }

    private function usuarioPossuiVinculos(User $user): bool
    {
        $tabelas = [
            'movimentacoes',
            'vendas',
            'nota_emissoes',
            'notificacoes',
            'audit_logs',
            'teams',
            'team_user',
        ];

        foreach ($tabelas as $tabela) {
            if (
                Schema::hasTable($tabela)
                && Schema::hasColumn($tabela, 'user_id')
                && DB::table($tabela)->where('user_id', $user->id)->exists()
            ) {
                return true;
            }
        }

        return false;
    }

    private function desativarUsuarioComVinculos(User $user)
    {
        if ($user->status !== 'inativo') {
            $user->forceFill(['status' => 'inativo'])->save();
        }

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuário possui vínculos no sistema e foi desativado em vez de excluído.');
    }

    private function isViolacaoChaveEstrangeira(QueryException $e): bool
    {
        return in_array($e->getCode(), ['23000', '23503'], true);
    }
}
