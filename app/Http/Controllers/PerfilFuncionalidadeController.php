<?php

namespace App\Http\Controllers;

use App\Models\Funcionalidade;
use App\Models\Perfil;
use Illuminate\Support\Facades\DB;

class PerfilFuncionalidadeController extends Controller
{
    public function edit()
    {
        $perfis = Perfil::query()
            ->where('ativo', true)
            ->orderBy('nome')
            ->get(['id', 'slug', 'nome']);

        $totalFuncionalidades = Funcionalidade::query()->where('ativo', true)->count();
        $qtdPorPerfil = DB::table('perfil_funcionalidade')
            ->selectRaw('perfil, COUNT(*) as total')
            ->groupBy('perfil')
            ->pluck('total', 'perfil');

        return view('perfil-funcionalidades.edit', compact('perfis', 'qtdPorPerfil', 'totalFuncionalidades'));
    }
}
