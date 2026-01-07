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
        $routeName = optional($request->route())->getName();

        // Superadmin ignora essa regra

        if ($user->perfil === 'superadmin') {
            return $next($request);
        }
        // Admins podem acessar apenas a rota da própria assinatura mesmo com pendências
        if ($user->isAdmin() && $routeName === 'assinaturas.minha') {
            return $next($request);
        }

        $assinatura = Assinaturas::where('empresa_id', $user->empresa_id)
            ->orderByDesc('id')
            ->first();
        if (!$assinatura) {
            return redirect()->route('assinaturas.expirada')
                ->with('error', 'Nenhuma assinatura ativa encontrada.');
        }

        if ($assinatura->emTesteAtivo()) {
            return $next($request);
        }

        if ($assinatura->em_teste && $assinatura->trial_expira_em && $assinatura->trial_expira_em->isPast()) {
            $dataExpiracao = $assinatura->trial_expira_em;
            $assinatura->update([
                'em_teste' => false,
                'trial_expira_em' => null,
                'status' => 'pendente',
            ]);

            return response()
                ->view('errors.expirada', [
                    'empresaNome' => $assinatura->empresa->nome,
                    'dataExpiracao' => $dataExpiracao,
                    'assinaturaId' => $assinatura->id,
                ]);
        }

        if ($assinatura->status === 'atrasado' || ($assinatura->data_vencimento && $assinatura->data_vencimento < now())) {
            $assinatura->update(['status' => 'atrasado']);

            return response()
                ->view('errors.expirada', [
                    'empresaNome' => $assinatura->empresa->nome,
                    'dataExpiracao' => $assinatura->data_vencimento,
                    'assinaturaId' => $assinatura->id,
                ]);
        }

        return $next($request);
    }
}
