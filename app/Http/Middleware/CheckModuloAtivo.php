<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckModuloAtivo
{
    public function handle(Request $request, Closure $next, $moduloSlug)
    {
        $empresa = auth()->user()->empresa;
        if (!$empresa) {
            abort(403, 'Empresa não encontrada.');
        }

        $modulo = $empresa->modulos()
            ->where('slug', $moduloSlug . '.index')
            ->first();

        if (!$modulo) {
            abort(403, 'Módulo não disponível.');
        }

        if ($modulo->pivot->status === 'bloqueado') {

            return response()->view('errors.modulo_bloqueado', [
                'moduloNome' => $modulo->nome
            ], 403);            // abort(403, 'Módulo bloqueado. Entre em contato para desbloquear.');
        }

        if ($modulo->pivot->status === 'expirado') {
            abort(403, 'Módulo expirado. Renove sua assinatura.');
        }

        return $next($request);
    }
}
