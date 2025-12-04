<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Modulo;
use Illuminate\Http\Request;

class EmpresaModuloController extends Controller
{
    public function edit($empresaId)
    {
        $empresas = Empresa::with('modulos')->findOrFail($empresaId);
        $modulos = Modulo::with('submodulos')->get();

        $modulosSelecionados = $empresas->modulos->pluck('pivot')->mapWithKeys(function ($pivot) {
            return [
                $pivot->modulo_id => [
                    'status' => $pivot->status,
                    'expira_em' => $pivot->expira_em,
                ],
            ];
        });

        return view('empresa.modulos', compact('empresas', 'modulos', 'modulosSelecionados'));
    }

    public function update(Request $request, $empresaId)
    {
        $empresa = Empresa::findOrFail($empresaId);

        $modulosSelecionados = collect($request->input('modulos', []))
            ->map(function ($dados, $id) {
                return [
                    'id' => (int) $id,
                    'status' => $dados['status'] ?? 'ativo',
                    'expira_em' => $dados['expira_em'] ?? null,
                ];
            })
            ->values();

        $empresa->modulos()->detach();

        foreach ($modulosSelecionados as $entry) {
            $empresa->modulos()->attach($entry['id'], [
                'status' => $entry['status'],
                'expira_em' => $entry['expira_em'],
                'ativo' => $entry['status'] === 'ativo',
            ]);
        }

        return redirect()->route('empresas.index')
            ->with('success', 'Permissões de módulos atualizadas com sucesso!');
    }
}
