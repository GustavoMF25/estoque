<?php

namespace App\Http\Middleware;

use App\Models\Assinaturas;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificaAssinaturaMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // Superadmin ignora essa regra
        if ($user->perfil === 'superadmin') {
            return $next($request);
        }

        $assinatura = Assinaturas::where('empresa_id', $user->empresa_id)
            ->orderByDesc('id')
            ->first();

        if (!$assinatura) {
            return redirect()->route('assinaturas.expirada')
                ->with('error', 'Nenhuma assinatura ativa encontrada.');
        }

        if ($assinatura->status === 'expirada' || $assinatura->data_vencimento < now()) {
            return redirect()->route('assinaturas.expirada')
                ->with('error', 'Sua assinatura expirou. Entre em contato para renovação.');
        }

        return $next($request);
    }
}
