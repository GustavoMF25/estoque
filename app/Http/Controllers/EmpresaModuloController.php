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
        $modulos = Modulo::all();

        return view('empresa.modulos', compact('empresas', 'modulos'));
    }

    public function update(Request $request, $empresaId)
    {
        $empresa = Empresa::findOrFail($empresaId);

        // Recebe IDs dos módulos marcados
        $modulosSelecionados = $request->input('modulos', []);

        // Obtenha os módulos associados à empresa
        $modulosAssociados = $empresa->modulos;

        // Verifica se o módulo está ativo ou bloqueado
        foreach ($modulosAssociados as $modulo) {
            // Se o módulo não está mais selecionado, marcaremos ele como 'bloqueado'
            if (!in_array($modulo->id, $modulosSelecionados)) {
                // Verifica se o módulo já está marcado como bloqueado
                $bloqueado = $modulo->pivot->status === 'bloqueado';

                if (!$bloqueado) {
                    // Marca o módulo como bloqueado
                    $modulo->pivot->status = 'bloqueado';
                    $modulo->pivot->save();
                }
            }
        }

        // Associa os módulos selecionados, garantindo que o status seja 'ativo'
        foreach ($modulosSelecionados as $moduloId) {
            // Verifica se o módulo já está associado
            $modulo = $empresa->modulos()->where('modulo_id', $moduloId)->first();

            if (!$modulo) {
                // Se não estiver associado, associamos com status 'ativo'
                $empresa->modulos()->attach($moduloId, ['status' => 'ativo']);
            } else {
                // Se já estiver associado, garantimos que o status seja 'ativo'
                if ($modulo->pivot->status === 'bloqueado') {
                    // Reativa o módulo, se estiver bloqueado
                    $modulo->pivot->status = 'ativo';
                    $modulo->pivot->save();
                }
            }
        }

        return redirect()->route('empresas.index')
            ->with('success', 'Permissões de módulos atualizadas com sucesso!');
    }
}
